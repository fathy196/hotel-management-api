<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'api']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'api']);

        // Create permissions
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
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }

        // Assign all permissions to admin
        $admin->givePermissionTo(Permission::all());

        // Assign limited permissions to other roles
        $receptionist->givePermissionTo([
            'manage bookings',
            'checkin checkout',
        ]);

        $customer->givePermissionTo([
            'view rooms',
            'create bookings',
            'view own bookings',
        ]);

        // Create a default admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'), // Change this for production
        ]);

        $adminUser->assignRole('admin');
    }
}
