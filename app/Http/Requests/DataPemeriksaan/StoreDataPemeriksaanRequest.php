<?php

namespace App\Http\Requests\DataPemeriksaan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi input angka pemeriksaan bulanan.
 * Keunikan (labkesmas + tes + periode) divalidasi berlapis: di sini (UX cepat) DAN di
 * level Action/DB unique constraint (integritas mutlak, anti race condition).
 */
class StoreDataPemeriksaanRequest extends FormRequest
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
            'jenis_tes_id' => ['required', 'string', 'exists:jenis_pemeriksaan,id'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'jumlah' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bulan.between' => 'Bulan harus antara 1 sampai 12.',
            'jumlah.min' => 'Jumlah pemeriksaan tidak boleh negatif.',
        ];
    }
}
