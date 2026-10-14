<?php

namespace App\Http\Requests;

use App\Enums\BookStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookCopyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('manage_copies');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $copyId = $this->route('book_copy'); // or route('id') or whatever parameter is used

        // If it's a bulk generation request, the rules will differ
        if ($this->has('bulk_count')) {
            return [
                'book_id' => ['required', 'exists:books,id'],
                'bulk_count' => ['required', 'integer', 'min:1', 'max:100'],
                'condition' => ['required', 'string', 'max:50'],
                'acquisition_date' => ['nullable', 'date'],
                'purchase_price' => ['nullable', 'numeric', 'min:0'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ];
        }

        return [
            'book_id' => ['required', 'exists:books,id'],
            'copy_code' => ['nullable', 'string', 'max:50', Rule::unique('book_copies', 'copy_code')->ignore($copyId)],
            'barcode' => ['nullable', 'string', 'max:50', Rule::unique('book_copies', 'barcode')->ignore($copyId)],
            'qr_code' => ['nullable', 'string', 'max:50', Rule::unique('book_copies', 'qr_code')->ignore($copyId)],
            'status' => ['required', Rule::enum(BookStatus::class)],
            'condition' => ['required', 'string', 'max:50'],
            'acquisition_date' => ['nullable', 'date', 'before_or_equal:today'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'book_id.required' => 'Buku wajib dipilih.',
            'book_id.exists' => 'Buku yang dipilih tidak valid.',
            'copy_code.unique' => 'Kode eksemplar sudah terdaftar.',
            'barcode.unique' => 'Barcode sudah terdaftar.',
            'qr_code.unique' => 'QR Code sudah terdaftar.',
            'status.required' => 'Status eksemplar wajib dipilih.',
            'condition.required' => 'Kondisi eksemplar wajib diisi.',
            'acquisition_date.before_or_equal' => 'Tanggal pengadaan tidak boleh melebihi hari ini.',
            'bulk_count.required' => 'Jumlah eksemplar wajib diisi.',
            'bulk_count.integer' => 'Jumlah eksemplar harus berupa angka.',
            'bulk_count.min' => 'Jumlah eksemplar minimal 1.',
            'bulk_count.max' => 'Jumlah eksemplar maksimal 100 sekali generate.',
        ];
    }
}
