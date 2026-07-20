<?php

namespace App\Http\Requests\StandarAlat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi sinkronisasi seluruh standar satu alat (semua tier sekaligus).
 */
class SyncStandarAlatRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.tier' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'items.*.jenis_lab' => ['required', 'string', Rule::in(['umum', 'biokes', 'kesling'])],
            'items.*.jumlah_minimal' => ['required', 'integer', 'min:0'],
        ];
    }
}
