<?php

namespace App\Http\Requests\User;

use App\Models\UserScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8'],
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
            'username.unique' => 'Username sudah dipakai.',
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }
}
