<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
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
            'with' => 'sometimes|array',
            'with.*' => 'sometimes|string|in:roles,permissions',
            'filters' => 'sometimes|array',
            'filters.role' => 'sometimes|string',
            'filters.email' => 'sometimes|string|max:255',
            'search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ];
    }
}
