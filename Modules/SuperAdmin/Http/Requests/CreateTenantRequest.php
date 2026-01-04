<?php

namespace Modules\SuperAdmin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $tenantId = $this->route('id'); // Get tenant ID for update operations

        return [
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'domains' => ['required', 'array', 'min:1'],
            'domains.*' => [
                'required',
                'string',
                'max:255',
                'distinct',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('tenants', 'email')->ignore($tenantId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tenants', 'phone')->ignore($tenantId),
            ],
            'is_active' => ['sometimes', 'boolean'],
            'password' => $this->isMethod('POST') && !$tenantId ? ['required', 'string', 'min:8'] : ['nullable', 'string', 'min:8'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $tenantId = $this->route('id');
            $domains = $this->input('domains', []);
            $existingDomains = [];

            // For update, get existing domains to exclude from unique validation
            if ($tenantId) {
                $tenant = \App\Models\Tenant::with('domains')->find($tenantId);
                if ($tenant) {
                    $existingDomains = $tenant->domains->pluck('domain')->toArray();
                }
            }

            // Validate each domain is unique (excluding existing ones for update)
            foreach ($domains as $index => $domain) {
                // For update, exclude current tenant's domains
                if ($tenantId && !empty($existingDomains) && in_array($domain, $existingDomains)) {
                    continue; // Skip validation for existing domains
                }

                $exists = \Illuminate\Support\Facades\DB::connection('central')
                    ->table('domains')
                    ->where('domain', $domain)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        "domains.{$index}",
                        __('validation.unique', ['attribute' => "domains.{$index}"])
                    );
                }
            }
        });
    }
}

