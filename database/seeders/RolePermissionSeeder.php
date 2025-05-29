<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles with 'api' guard
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $receptionist = Role::create(['name' => 'receptionist', 'guard_name' => 'api']);
        $customer = Role::create(['name' => 'customer', 'guard_name' => 'api']);

        $permissions = [
            'manage users',
            'manage rooms',
            'manage bookings',
            'manage payments',
            'view rooms',
            'create bookings',
            'view own bookings',
            'checkin checkout'
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }

        // Assign permissions
        $admin->givePermissionTo(Permission::all());

        $receptionist->givePermissionTo([
            'manage bookings',
            'checkin checkout',
        ]);

        $customer->givePermissionTo([
            'view rooms',
            'create bookings',
            'view own bookings',
        ]);
    }
}
