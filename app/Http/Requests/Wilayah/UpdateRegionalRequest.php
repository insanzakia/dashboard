<?php

namespace App\Http\Requests\Wilayah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Sama seperti StoreRegionalRequest, namun aturan unik mengabaikan record yang sedang diedit.
 */
class UpdateRegionalRequest extends FormRequest
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
        $regionalId = $this->route('regional');

        return [
            'nama' => [
                'required', 'string', 'max:150',
                Rule::unique('regional', 'nama')
                    ->where('negara_id', $this->input('negara_id'))
                    ->ignore($regionalId),
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
