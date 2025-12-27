<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

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
    ];

    /**
     * Get the list of real database columns (not virtual columns stored in data JSON).
     * This prevents VirtualColumn trait from storing these attributes in the data column.
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
        ];
    }
}
