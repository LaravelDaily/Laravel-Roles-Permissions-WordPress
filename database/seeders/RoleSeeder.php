<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'posts' => [
                'view',
                'create',
                'update',
                'delete',
            ],
            'settings' => [
                'view',
                'update',
            ],
        ];

        foreach ($permissions as $permission => $actions) {
            foreach ($actions as $action) {
                Permission::create(['name' => $permission.'-'.$action]);
            }
        }

        $roles = [
            'Super Admin' => [
                'posts-view',
                'posts-create',
                'posts-update',
                'posts-delete',
                'settings-view',
                'settings-update',
            ],
            'Subscriber' => [],
        ];

        foreach ($roles as $role => $permissionsList) {
            $role = Role::create(['name' => $role]);
            $role->syncPermissions($permissionsList);
        }
    }
}
