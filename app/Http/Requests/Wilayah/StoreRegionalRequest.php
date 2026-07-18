<?php

namespace App\Http\Requests\Wilayah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi input pembuatan Regional. Menegakkan:
 * - keberadaan parent (negara_id harus valid) → integritas FK,
 * - keunikan (nama, negara_id) → mencegah duplikasi (PRD Section 6),
 * - batasan tipe & panjang → robustness.
 */
class StoreRegionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi rute ditangani middleware `auth` (+ Policy saat RBAC ditambahkan).
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique('regional', 'nama')->where('negara_id', $this->input('negara_id')),
            ],
            'negara_id' => ['required', 'string', 'exists:negara,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.unique' => 'Regional dengan nama ini sudah terdaftar pada negara tersebut.',
            'negara_id.exists' => 'Negara yang dipilih tidak valid.',
        ];
    }
}
