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
                'publish',
                'edit-others',
            ],
            'settings' => [
                'view',
                'update',
            ],
            'users' => [
                'view',
                'create',
                'update',
                'delete',
            ],
        ];

        foreach ($permissions as $permission => $actions) {
            foreach ($actions as $action) {
                Permission::create(['name' => $permission . '-' . $action]);
            }
        }

        $roles = [
            'Admin' => [
                'posts-view',
                'posts-create',
                'posts-update',
                'posts-delete',
                'posts-publish',
                'posts-edit-others',
                'settings-view',
                'settings-update',
                'users-view',
                'users-create',
                'users-update',
                'users-delete',
            ],
            'Editor' => [
                'posts-view',
                'posts-create',
                'posts-update',
                'posts-delete',
                'posts-publish',
                'posts-edit-others',
            ],
            'Author' => [
                'posts-view',
                'posts-create',
                'posts-update',
                'posts-delete',
                'posts-publish',
            ],
            'Contributor' => [
                'posts-view',
                'posts-create',
                'posts-update',
                'posts-delete',
            ],
        ];

        foreach ($roles as $role => $permissionsList) {
            $role = Role::create(['name' => $role]);
            $role->syncPermissions($permissionsList);
        }
    }
}
