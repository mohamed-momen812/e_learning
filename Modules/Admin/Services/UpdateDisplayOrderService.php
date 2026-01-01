<?php

namespace Modules\Admin\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class UpdateDisplayOrderService
{
    /**
     * Update display order for users
     * 
     * @param array $orders - Array of ['id' => 1, 'display_order' => 2]
     * @return bool
     */
    public function updateUserOrder(array $orders): bool
    {
        return DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                User::where('id', $order['id'])
                    ->update(['display_order' => $order['display_order']]);
            }
            return true;
        });
    }

    /**
     * Reorder users by IDs in order
     * 
     * @param array $ids - Array of IDs in desired order [1, 3, 2, 5]
     * @return bool
     */
    public function reorderUsersByIds(array $ids): bool
    {
        return DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                User::where('id', $id)
                    ->update(['display_order' => $index + 1]);
            }
            return true;
        });
    }

    /**
     * Update display order for roles
     * 
     * @param array $orders - Array of ['id' => 1, 'display_order' => 2]
     * @return bool
     */
    public function updateRoleOrder(array $orders): bool
    {
        return DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                Role::where('id', $order['id'])
                    ->where('guard_name', 'web')
                    ->update(['display_order' => $order['display_order']]);
            }
            return true;
        });
    }

    /**
     * Reorder roles by IDs in order
     * 
     * @param array $ids - Array of IDs in desired order [1, 3, 2, 5]
     * @return bool
     */
    public function reorderRolesByIds(array $ids): bool
    {
        return DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Role::where('id', $id)
                    ->where('guard_name', 'web')
                    ->update(['display_order' => $index + 1]);
            }
            return true;
        });
    }

    /**
     * Update display order for permissions
     * 
     * @param array $orders - Array of ['id' => 1, 'display_order' => 2]
     * @return bool
     */
    public function updatePermissionOrder(array $orders): bool
    {
        return DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                Permission::where('id', $order['id'])
                    ->where('guard_name', 'web')
                    ->update(['display_order' => $order['display_order']]);
            }
            return true;
        });
    }

    /**
     * Reorder permissions by IDs in order
     * 
     * @param array $ids - Array of IDs in desired order [1, 3, 2, 5]
     * @return bool
     */
    public function reorderPermissionsByIds(array $ids): bool
    {
        return DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Permission::where('id', $id)
                    ->where('guard_name', 'web')
                    ->update(['display_order' => $index + 1]);
            }
            return true;
        });
    }
}
