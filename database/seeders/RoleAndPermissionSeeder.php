<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // DIVERSOS
        $miscPermission = Permission::create(['name' => 'N/A']);

        // USER MODEL
        $permissionCreateUser = Permission::create(['name' => 'create user']);
        $permissionReadUser = Permission::create(['name' => 'read user']);
        $permissionUpdateUser = Permission::create(['name' => 'update user']);
        $permissionDeleteUser = Permission::create(['name' => 'delete user']);

        // ROLE MODEL
        $permissionCreateRole = Permission::create(['name' => 'create role']);
        $permissionReadRole = Permission::create(['name' => 'read role']);
        $permissionUpdateRole = Permission::create(['name' => 'update role']);
        $permissionDeleteRole = Permission::create(['name' => 'delete role']);

        // PERMISSION MODEL
        $permissionCreatePermission = Permission::create(['name' => 'create permission']);
        $permissionReadPermission = Permission::create(['name' => 'read permission']);
        $permissionUpdatePermission = Permission::create(['name' => 'update permission']);
        $permissionDeletePermission = Permission::create(['name' => 'delete permission']);

        // ADMINS
        $permissionReadAdmin = Permission::create(['name' => 'read admin']);
        $permissionUpdateAdmin = Permission::create(['name' => 'update admin']);

        // DEBUG
        $permissionManageDebug = Permission::create(['name' => 'manage debug']);

        // CREATE ROLES
        $superAdminRole = Role::create(['name' => 'super-admin']);

        $adminRole = Role::create(['name' => 'admin'])->syncPermissions([
            $permissionCreateUser,
            $permissionReadUser,
            $permissionUpdateUser,
            $permissionDeleteUser,

            $permissionCreateRole,
            $permissionReadRole,
            $permissionUpdateRole,
            $permissionDeleteRole,

            $permissionCreatePermission,
            $permissionReadPermission,
            $permissionUpdatePermission,
            $permissionDeletePermission,

            $permissionReadAdmin,
            $permissionUpdateAdmin,

            $permissionManageDebug,
        ]);

        $moderatorRole = Role::create(['name' => 'moderator'])->syncPermissions([
            $permissionReadUser,

            $permissionReadRole,

            $permissionReadPermission,

            $permissionReadAdmin,
        ]);

        $developerRole = Role::create(['name' => 'developer'])->syncPermissions([
            $permissionReadUser,
        ]);

        $userRole = Role::create(['name' => 'user'])->syncPermissions([
            $miscPermission,
        ]);

        // CREATE ADMINS & USERS
        User::create([
            'name' => 'Super admin',
            'access_admin' => 1,
            'email' => 'super@super.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'remember_token' => Str::random(10),
        ])->assignRole($superAdminRole);

        User::create([
            'name' => 'Admin',
            'access_admin' => 1,
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'remember_token' => Str::random(10),
        ])->assignRole($adminRole);

        User::create([
            'name' => 'Moderator',
            'access_admin' => 1,
            'email' => 'moderator@moderator.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'remember_token' => Str::random(10),
        ])->assignRole($moderatorRole);

        User::create([
            'name' => 'Developer',
            'access_admin' => 1,
            'email' => 'developer@developer.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'remember_token' => Str::random(10),
        ])->assignRole($developerRole);

        for ($i = 1; $i < 50; $i++) {
            User::create([
                'name' => 'Test '.$i,
                'access_admin' => 0,
                'email' => 'test'.$i.'@test.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(10),
            ])->assignRole($userRole);
        }
    }
}
