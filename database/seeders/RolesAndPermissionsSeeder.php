<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage_all']);
        Permission::create(['name' => 'manage_users']);
        
        // create roles and assign created permissions
        $role = Role::create(['name' => 'superadmin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(['manage_all', 'manage_users']);

        $role = Role::create(['name' => 'registered']);
        $role = Role::create(['name' => 'subscriber']);
        $role = Role::create(['name' => 'premium']);

        // register an admin user
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('abc12345'),
        ]);
        $user->assignRole('superadmin');
    }


}


    