<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('roles.view') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'with' => 'sometimes|array',
            'with.*' => 'sometimes|string|in:permissions',
            'filters' => 'sometimes|array',
            'filters.name' => 'sometimes|string|max:255',
            'filters.permission' => 'sometimes|string',
            'filters.created_at_from' => 'sometimes|date',
            'filters.created_at_to' => 'sometimes|date',
            'search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ];
    }
}
