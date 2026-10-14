<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage_books');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book'); // Returns book model/ID depending on routing/binding

        return [
            'isbn' => ['nullable', 'string', 'max:50', Rule::unique('books', 'isbn')->ignore($bookId)],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publish_year' => ['nullable', 'string', 'regex:/^[0-9]{4}$/'],
            'edition' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'language' => ['nullable', 'string', 'max:100'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'rack_location' => ['nullable', 'string', 'max:100'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_digital' => ['nullable', 'boolean'],
            'digital_source' => ['nullable', 'string', 'max:255'],
            'digital_url' => ['nullable', 'required_if:is_digital,1', 'url', 'max:2048'],
            'digital_format' => ['nullable', 'string', 'max:100'],
            'digital_grade_level' => ['nullable', 'string', 'max:100'],
            'digital_access_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!$this->boolean('is_digital') || !$this->filled('digital_url')) {
                return;
            }

            $host = parse_url($this->input('digital_url'), PHP_URL_HOST);
            $scheme = parse_url($this->input('digital_url'), PHP_URL_SCHEME);

            if ($scheme !== 'https' || !in_array($host, ['buku.kemendikdasmen.go.id'], true)) {
                $validator->errors()->add('digital_url', 'Link buku digital harus menggunakan sumber resmi https://buku.kemendikdasmen.go.id/.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Pengarang buku wajib diisi.',
            'isbn.unique' => 'ISBN sudah terdaftar.',
            'publish_year.regex' => 'Format tahun terbit harus berupa 4 digit angka (contoh: 2024).',
            'cover_image.image' => 'File cover harus berupa gambar.',
            'cover_image.max' => 'Ukuran cover maksimal 2MB.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'price.numeric' => 'Harga harus berupa angka.',
            'page_count.integer' => 'Jumlah halaman harus berupa angka bulat.',
            'digital_url.required_if' => 'Link resmi buku digital wajib diisi jika buku ditandai digital.',
            'digital_url.url' => 'Link buku digital harus berupa URL yang valid.',
        ];
    }
}
