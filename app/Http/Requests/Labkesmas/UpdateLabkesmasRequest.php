<?php

namespace App\Http\Requests\Labkesmas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLabkesmasRequest extends FormRequest
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
            'nama_kantor' => ['required', 'string', 'max:200'],
            'tier_labkesmas' => ['required', 'integer', Rule::in([2, 3, 4, 5])],
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
            'kabupaten_kota_id.exists' => 'Kabupaten/Kota yang dipilih tidak valid.',
        ];
    }
}
