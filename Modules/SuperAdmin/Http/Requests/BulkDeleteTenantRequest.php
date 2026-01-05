<?php

namespace Modules\SuperAdmin\Http\Requests;

use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('bulkDelete', Tenant::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:tenants,id'],
        ];
    }
}
