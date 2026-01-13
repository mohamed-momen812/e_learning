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
            'phone' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '') {
                        return; // Allow null/empty values since it's nullable
                    }

                    // Remove spaces, dashes, and parentheses
                    $cleaned = preg_replace('/[\s\-()]/', '', $value);

                    // Egypt phone patterns:
                    // 01 followed by 9 digits (01XXXXXXXXX)
                    // 201 or +201 followed by 9 digits (201XXXXXXXXX or +201XXXXXXXXX)
                    $egyptPattern = '/^(01\d{9}|(\+?20)?1\d{9})$/';

                    if (!preg_match($egyptPattern, $cleaned)) {
                        $fail(__('validation.phone.egypt', ['attribute' => $attribute]));
                    }
                },
            ],
            'password' => ['sometimes', 'string', 'min:8'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['sometimes', 'string', Rule::exists('roles', 'name')->where('guard_name', 'web')],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'], // 2MB max
        ];
    }
}
