<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user?->id),
            ],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'validation.string',
            'name.max' => 'validation.max',
            'email.email' => 'validation.email',
            'email.unique' => 'validation.unique',
            'phone.string' => 'validation.string',
            'phone.max' => 'validation.max',
        ];
    }
}

