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
        // TODO: Add super admin authorization check
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
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'validation.required',
            'name.string' => 'validation.string',
            'name.max' => 'validation.max',
            'domain.required' => 'validation.required',
            'domain.string' => 'validation.string',
            'domain.max' => 'validation.max',
            'domain.unique' => 'validation.unique',
        ];
    }
}

