<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller via UserPolicy
        // which checks ownership and permissions
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->route('user');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['sometimes', 'string', 'min:8'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['sometimes', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'], // 2MB max
        ];
    }
}

