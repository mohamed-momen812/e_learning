<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDisplayOrderRequest extends FormRequest
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
            // Option 1: Array of objects with id and display_order
            'orders' => 'sometimes|array',
            'orders.*.id' => 'required_with:orders|integer|exists:users,id',
            'orders.*.display_order' => 'required_with:orders|integer|min:0',

            // Option 2: Simple array of IDs in order (simpler for frontend)
            'ids' => 'required_without:orders|array',
            'ids.*' => 'required_without:orders|integer|exists:users,id',
        ];
    }
}
