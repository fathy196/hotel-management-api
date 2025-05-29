<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $admin = Role::create(['name' => 'admin']);
        $receptionist = Role::create(['name' => 'receptionist']);
        $customer = Role::create(['name' => 'customer']);

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
            Permission::create(['name' => $permission]);
        }

        $admin->givePermissionTo(Permission::all());
        $receptionist->givePermissionTo(['manage bookings', 'checkin checkout']);
        $customer->givePermissionTo(['view rooms', 'create bookings', 'view own bookings']);
    }
}
