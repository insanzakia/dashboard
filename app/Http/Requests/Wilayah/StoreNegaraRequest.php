<?php

namespace App\Http\Requests\Wilayah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNegaraRequest extends FormRequest
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
        // Negara adalah puncak hierarki → tidak punya parent; nama unik global.
        return [
            'nama' => ['required', 'string', 'max:150', Rule::unique('negara', 'nama')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.unique' => 'Negara dengan nama ini sudah terdaftar.',
        ];
    }
}
