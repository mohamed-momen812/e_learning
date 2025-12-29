<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('teacher') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->where('guard_name', 'web')],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['sometimes', 'string', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'validation.required',
            'name.unique' => 'validation.unique',
        ];
    }
}

