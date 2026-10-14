<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\MemberType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Member::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>
     */
    public function rules(): array
    {
        $memberId = $this->route('member');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('members', 'email')->ignore($memberId)],
            'nisn' => ['nullable', 'string', 'max:20', Rule::unique('members', 'nisn')->ignore($memberId)],
            'employee_number' => ['nullable', 'string', 'max:20', Rule::unique('members', 'employee_number')->ignore($memberId)],
            'member_number' => ['nullable', 'string', 'max:50', Rule::unique('members', 'member_number')->ignore($memberId)],
            'type' => ['required', Rule::enum(MemberType::class)],
            'class' => ['nullable', 'string', 'max:50'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:12'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'photo' => ['nullable', 'image', 'max:2048'],
            'join_date' => ['nullable', 'date', 'after_or_equal:2000-01-01'],
            'expired_date' => ['nullable', 'date', 'after:join_date'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'blocked'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'employee_number.unique' => 'NIP sudah terdaftar.',
            'member_number.unique' => 'Nomor anggota sudah terdaftar.',
            'type.required' => 'Tipe anggota wajib dipilih.',
            'birth_date.before' => 'Tanggal lahir tidak valid.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert type string to enum value if needed
        if ($this->has('type') && is_string($this->type)) {
            $this->merge(['type' => strtolower($this->type)]);
        }
    }
}