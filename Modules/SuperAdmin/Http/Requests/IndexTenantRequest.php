<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexTenantRequest extends FormRequest
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
            'with' => 'sometimes|array',
            'with.*' => 'sometimes|string|in:domains',
            'filters' => 'sometimes|array',
            'filters.name' => 'sometimes|string|max:255',
            'filters.is_active' => 'sometimes|boolean',
            'search' => 'sometimes|string|max:255',
            'sort' => 'sometimes|string',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ];
    }
}

