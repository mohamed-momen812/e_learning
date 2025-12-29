<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
        $roleId = $this->route('id') ?? $this->route('role');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('roles')->where('guard_name', 'web')->ignore($roleId)],
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
            'name.unique' => 'validation.unique',
        ];
    }
}

