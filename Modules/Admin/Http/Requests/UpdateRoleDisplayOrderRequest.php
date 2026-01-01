<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleDisplayOrderRequest extends FormRequest
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
        return [
            'orders' => 'sometimes|array',
            'orders.*.id' => 'required_with:orders|integer|exists:roles,id',
            'orders.*.display_order' => 'required_with:orders|integer|min:0',

            'ids' => 'required_without:orders|array',
            'ids.*' => 'required_without:orders|integer|exists:roles,id',
        ];
    }
}
