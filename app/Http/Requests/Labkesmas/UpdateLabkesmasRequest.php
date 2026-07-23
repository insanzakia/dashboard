<?php

namespace App\Http\Requests\Labkesmas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLabkesmasRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $labkesmas = $this->route('labkesmas');

        // Harus punya akses ke lab yang diedit DAN kab/kota tujuan ada dalam cakupan
        // (mencegah memindahkan lab keluar dari wilayahnya). super_admin lolos semua.
        return $user->canAccessLabkesmas($labkesmas?->id)
            && $user->canCreateLabkesmasInKab($this->input('kabupaten_kota_id'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_kantor' => ['required', 'string', 'max:200'],
            'tier_labkesmas' => ['required', 'integer', Rule::in([2, 3, 4, 5])],
            // Jenis lab wajib hanya untuk tier 5 (lab terpisah: biokes/kesling); tier 2-4 kosong.
            'jenis_lab' => ['nullable', 'required_if:tier_labkesmas,5', Rule::in(['biokes', 'kesling'])],
            'kabupaten_kota_id' => ['required', 'string', 'exists:kabupaten_kota,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tier_labkesmas.in' => 'Tier labkesmas harus bernilai 2, 3, 4, atau 5.',
            'jenis_lab.required_if' => 'Jenis lab (Biokes/Kesling) wajib dipilih untuk tier 5.',
            'jenis_lab.in' => 'Jenis lab harus Biokes atau Kesling.',
            'kabupaten_kota_id.exists' => 'Kabupaten/Kota yang dipilih tidak valid.',
        ];
    }
}
