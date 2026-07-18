<?php

namespace App\Http\Requests\Wilayah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKabupatenKotaRequest extends FormRequest
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
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique('kabupaten_kota', 'nama')
                    ->where('provinsi_id', $this->input('provinsi_id'))
                    ->ignore($this->route('kabupatenKota')),
            ],
            'provinsi_id' => ['required', 'string', 'exists:provinsi,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.unique' => 'Kabupaten/Kota dengan nama ini sudah terdaftar pada provinsi tersebut.',
            'provinsi_id.exists' => 'Provinsi yang dipilih tidak valid.',
        ];
    }
}
