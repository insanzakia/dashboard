<?php

namespace App\Http\Requests\StandarAlat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi penyimpanan standar jumlah minimal satu alat (UPSERT per tier & jenis lab).
 */
class StoreStandarAlatRequest extends FormRequest
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
            'alat_id' => ['required', 'string', 'exists:alat,id'],
            'tier' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'jenis_lab' => ['required', 'string', Rule::in(['umum', 'biokes', 'kesling'])],
            'jumlah_minimal' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tier.in' => 'Tier harus bernilai 1 sampai 5.',
            'jenis_lab.in' => 'Jenis lab harus umum, biokes, atau kesling.',
            'jumlah_minimal.min' => 'Jumlah minimal tidak boleh negatif.',
        ];
    }
}
