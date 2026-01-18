<?php

namespace Modules\Tenants\Models;

use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasTranslations;

    /**
     * Translatable attributes
     */
    public $translatable = [
        'name',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'data',
        'display_order',
    ];

    protected $casts = [
        'data' => 'array',
        'name' => 'array',
    ];

    /**
     * Get the list of real database columns (not virtual columns stored in data JSON).
     * This prevents VirtualColumn trait from storing these attributes in the data column.
     *
     * @return array<int, string>
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'password',
            'is_active',
            'display_order',
        ];
    }
}
