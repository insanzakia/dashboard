<?php

namespace App\Http\Requests\InventarisAlat;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi input massal kepemilikan alat satu Labkesmas.
 * `items` = daftar { alat_id, jumlah } untuk seluruh alat yang diisi sekaligus.
 */
class StoreInventarisAlatRequest extends FormRequest
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
            'labkesmas_id' => ['required', 'string', 'exists:labkesmas,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.alat_id' => ['required', 'string', 'exists:alat,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Tidak ada data alat untuk disimpan.',
            'items.*.jumlah.min' => 'Jumlah alat tidak boleh negatif.',
        ];
    }
}
