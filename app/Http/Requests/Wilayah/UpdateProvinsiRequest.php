<?php

namespace App\Http\Requests\Wilayah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProvinsiRequest extends FormRequest
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
                Rule::unique('provinsi', 'nama')
                    ->where('regional_id', $this->input('regional_id'))
                    ->ignore($this->route('provinsi')),
            ],
            'regional_id' => ['required', 'string', 'exists:regional,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.unique' => 'Provinsi dengan nama ini sudah terdaftar pada regional tersebut.',
            'regional_id.exists' => 'Regional yang dipilih tidak valid.',
        ];
    }
}
