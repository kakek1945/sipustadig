<?php

namespace App\Services;

use App\Enums\BookStatus;
use App\Enums\MemberType;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Member;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportService
{
    public function templates(): array
    {
        return [
            'members' => ['name', 'email', 'type', 'nisn', 'employee_number', 'class', 'year_level', 'phone', 'address', 'gender'],
            'books' => ['isbn', 'title', 'author', 'publisher', 'publish_year', 'category_code', 'rack_location', 'price', 'description', 'is_digital', 'digital_url', 'digital_source', 'digital_format', 'digital_grade_level', 'digital_access_note'],
            'copies' => ['book_isbn', 'copy_code', 'barcode', 'status', 'condition', 'acquisition_date', 'purchase_price', 'notes'],
        ];
    }

    public function import(string $type, UploadedFile $file, bool $dryRun = false): array
    {
        abort_unless(array_key_exists($type, $this->templates()), 404);

        $rows = $this->parseRows($file);
        $errors = [];
        $created = 0;
        $preview = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $line = $index + 2;
                $result = match ($type) {
                    'members' => $this->importMember($row, $line),
                    'books' => $this->importBook($row, $line),
                    'copies' => $this->importCopy($row, $line),
                };

                if ($result['errors']) {
                    $errors = array_merge($errors, $result['errors']);
                    continue;
                }

                $created++;
                if (count($preview) < 10) {
                    $preview[] = $result['preview'];
                }
            }

            if ($dryRun || $errors) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'type' => $type,
            'total' => count($rows),
            'created' => $errors ? 0 : $created,
            'valid' => $created,
            'errors' => $errors,
            'preview' => $preview,
            'dry_run' => $dryRun,
        ];
    }

    public function templateResponse(string $type)
    {
        abort_unless(array_key_exists($type, $this->templates()), 404);

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $this->templates()[$type]);
            fputcsv($handle, $this->sampleRow($type));
            fclose($handle);
        }, "template-{$type}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function parseRows(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'xlsx') {
            return $this->parseXlsx($file);
        }

        return $this->parseCsv($file);
    }

    protected function parseCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        if (!$header) {
            return [];
        }

        $header = array_map(fn ($value) => trim((string) $value), $header);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if ($data === [null] || trim(implode('', $data)) === '') {
                continue;
            }

            $row = [];
            foreach ($header as $index => $key) {
                $row[$key] = isset($data[$index]) ? trim((string) $data[$index]) : null;
            }
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    protected function parseXlsx(UploadedFile $file): array
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('Import XLSX membutuhkan ekstensi PHP ZipArchive. Gunakan CSV jika ekstensi belum tersedia.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new \RuntimeException('File XLSX tidak dapat dibaca.');
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $xml = simplexml_load_string($sharedXml);
            foreach ($xml->si ?? [] as $string) {
                $sharedStrings[] = (string) ($string->t ?? $string->r->t ?? '');
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new \RuntimeException('Sheet pertama tidak ditemukan di file XLSX.');
        }

        $xml = simplexml_load_string($sheetXml);
        $table = [];

        foreach ($xml->sheetData->row ?? [] as $row) {
            $values = [];
            foreach ($row->c ?? [] as $cell) {
                $reference = (string) $cell['r'];
                $column = $this->columnIndex(preg_replace('/\d+/', '', $reference));
                $value = (string) ($cell->v ?? '');

                if ((string) $cell['t'] === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                }

                $values[$column] = trim($value);
            }

            if ($values !== []) {
                ksort($values);
                $table[] = $values;
            }
        }

        if ($table === []) {
            return [];
        }

        $header = array_map(fn ($value) => trim((string) $value), array_values($table[0]));
        $rows = [];

        foreach (array_slice($table, 1) as $data) {
            $row = [];
            foreach ($header as $index => $key) {
                $row[$key] = $data[$index] ?? null;
            }
            if (trim(implode('', $row)) !== '') {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    protected function columnIndex(string $letters): int
    {
        $index = 0;
        foreach (str_split(strtoupper($letters)) as $letter) {
            $index = $index * 26 + (ord($letter) - 64);
        }

        return $index - 1;
    }

    protected function importMember(array $row, int $line): array
    {
        $validator = Validator::make($row, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:members,email'],
            'type' => ['required', Rule::in(MemberType::values())],
            'nisn' => ['nullable', 'string', 'max:20', 'unique:members,nisn'],
            'employee_number' => ['nullable', 'string', 'max:20', 'unique:members,employee_number'],
            'class' => ['nullable', 'string', 'max:50'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:12'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
        ]);

        if ($validator->fails()) {
            return $this->failed($line, $validator->errors()->all());
        }

        $member = Member::create([
            'member_number' => Member::generateMemberNumber(),
            'name' => $row['name'],
            'email' => $row['email'],
            'type' => $row['type'],
            'nisn' => $row['nisn'] ?: null,
            'employee_number' => $row['employee_number'] ?: null,
            'class' => $row['class'] ?: null,
            'year_level' => $row['year_level'] ?: null,
            'phone' => $row['phone'] ?: null,
            'address' => $row['address'] ?: null,
            'gender' => $row['gender'] ?: null,
            'join_date' => now()->toDateString(),
            'expired_date' => now()->addYears((int) app('settings')->get('member.card_validity_years', 3))->toDateString(),
            'status' => 'active',
        ]);

        return ['errors' => [], 'preview' => [$member->member_number, $member->name, $member->email, $member->type->label()]];
    }

    protected function importBook(array $row, int $line): array
    {
        $validator = Validator::make($row, [
            'isbn' => ['nullable', 'string', 'max:50', 'unique:books,isbn'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publish_year' => ['nullable', 'regex:/^[0-9]{4}$/'],
            'category_code' => ['nullable', 'string', 'max:50'],
            'rack_location' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_digital' => ['nullable'],
            'digital_url' => ['nullable', 'url', 'max:2048'],
            'digital_source' => ['nullable', 'string', 'max:255'],
            'digital_format' => ['nullable', 'string', 'max:100'],
            'digital_grade_level' => ['nullable', 'string', 'max:100'],
            'digital_access_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return $this->failed($line, $validator->errors()->all());
        }

        $isDigital = filter_var($row['is_digital'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $digitalUrl = $row['digital_url'] ?? null;

        if ($isDigital && empty($digitalUrl)) {
            return $this->failed($line, ['Link resmi buku digital wajib diisi.']);
        }

        if ($isDigital && !$this->isOfficialDigitalUrl($digitalUrl)) {
            return $this->failed($line, ['Link buku digital harus menggunakan sumber resmi https://buku.kemendikdasmen.go.id/.']);
        }

        $category = null;
        if (!empty($row['category_code'])) {
            $category = Category::firstOrCreate(
                ['code' => strtoupper($row['category_code'])],
                ['name' => strtoupper($row['category_code']), 'color' => '#0969da']
            );
        }

        $book = Book::create([
            'isbn' => $row['isbn'] ?: null,
            'title' => $row['title'],
            'author' => $row['author'],
            'publisher' => $row['publisher'] ?: null,
            'publish_year' => $row['publish_year'] ?: null,
            'category_id' => $category?->id,
            'rack_location' => $row['rack_location'] ?: null,
            'price' => $row['price'] ?: null,
            'description' => $row['description'] ?: null,
            'is_digital' => $isDigital,
            'digital_source' => $isDigital ? (($row['digital_source'] ?? null) ?: 'SIBI/Kemendikdasmen') : null,
            'digital_url' => $isDigital ? $digitalUrl : null,
            'digital_format' => $isDigital ? (($row['digital_format'] ?? null) ?: null) : null,
            'digital_grade_level' => $isDigital ? (($row['digital_grade_level'] ?? null) ?: null) : null,
            'digital_access_note' => $isDigital ? (($row['digital_access_note'] ?? null) ?: null) : null,
        ]);

        return ['errors' => [], 'preview' => [$book->isbn, $book->title, $book->author, $book->category?->name ?? '-']];
    }

    protected function importCopy(array $row, int $line): array
    {
        $validator = Validator::make($row, [
            'book_isbn' => ['required_without:book_id', 'nullable', 'string'],
            'book_id' => ['nullable', 'integer', 'exists:books,id'],
            'copy_code' => ['nullable', 'string', 'max:50', 'unique:book_copies,copy_code'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:book_copies,barcode'],
            'status' => ['nullable', Rule::in(BookStatus::values())],
            'condition' => ['nullable', 'string', 'max:50'],
            'acquisition_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return $this->failed($line, $validator->errors()->all());
        }

        $book = !empty($row['book_id'])
            ? Book::find($row['book_id'])
            : Book::where('isbn', $row['book_isbn'])->first();

        if (!$book) {
            return $this->failed($line, ['Buku tidak ditemukan untuk ISBN/ID yang diberikan.']);
        }

        $copyCode = $row['copy_code'] ?: BookCopy::generateCopyCode($book);
        $copy = BookCopy::create([
            'book_id' => $book->id,
            'copy_code' => $copyCode,
            'barcode' => $row['barcode'] ?: $copyCode,
            'qr_code' => $copyCode,
            'status' => $row['status'] ?: BookStatus::Available->value,
            'condition' => $row['condition'] ?: 'Baik',
            'acquisition_date' => $row['acquisition_date'] ?: now()->toDateString(),
            'purchase_price' => $row['purchase_price'] ?: null,
            'notes' => $row['notes'] ?: null,
        ]);

        return ['errors' => [], 'preview' => [$copy->copy_code, $book->title, $copy->status->label(), $copy->condition]];
    }

    protected function isOfficialDigitalUrl(?string $url): bool
    {
        if (!$url) {
            return false;
        }

        return parse_url($url, PHP_URL_SCHEME) === 'https'
            && parse_url($url, PHP_URL_HOST) === 'buku.kemendikdasmen.go.id';
    }

    protected function failed(int $line, array $messages): array
    {
        return [
            'errors' => array_map(fn ($message) => "Baris {$line}: {$message}", $messages),
            'preview' => [],
        ];
    }

    protected function sampleRow(string $type): array
    {
        return match ($type) {
            'members' => ['Budi Santoso', 'budi@example.test', 'student', '1234567890', '', 'X IPA 1', '10', '08123456789', 'Jl. Sekolah', 'male'],
            'books' => ['9786020000001', 'Matematika Dasar', 'Tim Guru', 'Pustaka Sekolah', '2024', 'MTK', 'Rak A1', '50000', 'Buku pelajaran', '0', '', '', '', '', ''],
            'copies' => ['9786020000001', 'MTK-001', 'MTK-001', 'available', 'Baik', now()->toDateString(), '50000', ''],
        };
    }
}
