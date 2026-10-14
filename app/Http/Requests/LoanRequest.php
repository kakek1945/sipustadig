<?php

namespace App\Http\Requests;

use App\Models\Loan;
use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Loan::class);
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', 'integer', 'exists:members,id'],
            'book_copy_ids' => ['required', 'array', 'min:1'],
            'book_copy_ids.*' => ['required', 'integer', 'distinct', 'exists:book_copies,id'],
            'loan_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_id.required' => 'Anggota wajib dipilih.',
            'member_id.exists' => 'Anggota tidak ditemukan.',
            'book_copy_ids.required' => 'Minimal satu eksemplar buku wajib dipilih.',
            'book_copy_ids.min' => 'Minimal satu eksemplar buku wajib dipilih.',
            'book_copy_ids.*.distinct' => 'Eksemplar buku tidak boleh dipilih lebih dari satu kali.',
            'book_copy_ids.*.exists' => 'Eksemplar buku tidak ditemukan.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('book_copy_id') && !$this->has('book_copy_ids')) {
            $this->merge(['book_copy_ids' => [$this->input('book_copy_id')]]);
        }

        if ($this->has('book_copy_ids')) {
            $this->merge([
                'book_copy_ids' => array_values(array_filter((array) $this->input('book_copy_ids'))),
            ]);
        }
    }
}
