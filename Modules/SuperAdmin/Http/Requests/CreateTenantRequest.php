<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller via TenantPolicy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'teacher_user' => ['required', 'array'],
            'teacher_user.name' => ['required', 'string', 'max:255'],
            'teacher_user.email' => ['required', 'email', 'max:255'],
            'teacher_user.password' => ['required', 'string', 'min:8'],
            'teacher_user.phone' => ['nullable', 'string', 'max:255'],
        ];
    }
}

