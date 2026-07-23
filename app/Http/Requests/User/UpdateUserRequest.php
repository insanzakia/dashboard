<?php

namespace App\Http\Requests\User;

use App\Models\UserScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Password opsional: diisi hanya bila ingin mereset.
            'password' => ['nullable', 'string', 'min:8'],
            'scopes' => ['nullable', 'array'],
            'scopes.*.scope_type' => ['required', Rule::in(UserScope::TYPES)],
            'scopes.*.scope_id' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }
}
