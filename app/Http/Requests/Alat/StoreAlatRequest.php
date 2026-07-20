<?php

namespace App\Http\Requests\Alat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlatRequest extends FormRequest
{
    /** 8 kategori peralatan sesuai KMK (harus sama dengan KATEGORI_ALAT di frontend). */
    public const KATEGORI = [
        'hematologi_kimia_imunologi',
        'mikrobiologi',
        'biomolekuler',
        'kesehatan_lingkungan',
        'toksikologi',
        'vektor_bpp',
        'penunjang',
        'kalibrasi',
    ];

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
            'nama_alat' => ['required', 'string', 'max:200'],
            'kategori' => ['required', 'string', Rule::in(self::KATEGORI)],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kategori.in' => 'Kategori alat tidak valid.',
        ];
    }
}
