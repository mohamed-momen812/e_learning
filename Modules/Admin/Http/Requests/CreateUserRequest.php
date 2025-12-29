<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['teacher', 'assistant']) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['sometimes', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'validation.required',
            'email.required' => 'validation.required',
            'email.email' => 'validation.email',
            'email.unique' => 'validation.unique',
            'password.required' => 'validation.required',
            'password.min' => 'validation.min.string',
        ];
    }
}

