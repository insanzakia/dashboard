<?php

namespace App\Http\Requests\JenisPemeriksaan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJenisPemeriksaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_tes' => [
                'required', 'string', 'max:150',
                Rule::unique('jenis_pemeriksaan', 'nama_tes')->ignore($this->route('jenisPemeriksaan')),
            ],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_tes.unique' => 'Jenis pemeriksaan dengan nama ini sudah terdaftar.',
        ];
    }
}
