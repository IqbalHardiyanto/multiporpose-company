<?php

namespace Database\Seeders;

use App\Models\User;
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
        //
        $permissions = [
            'manage statistics',
            'manage products',
            'manage principles',
            'manage teams',
            'manage testimonials',
            'manage clients',
            'manage abouts',
            'manage appointments',
            'manage hero sections'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }


        // designer role
        $designManagerRole = Role::firstOrcreate([
            'name' => 'design_manager'
        ]);

        $designManagerPermission = [
            'manage products',
            'manage principles',
            'manage testimonials'
        ];
        $designManagerRole->syncPermissions($designManagerPermission);


        // admin role
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin'
        ]);

        $superAdminRole->givePermissionTo($permissions);

        $user = User::firstOrCreate(
            [
                'email' => 'super_admin@mail.com'
            ],
            [
                'name' => 'AdminComps',
                'password' => bcrypt('123123123'),
            ]
        );

        $user->assignRole($superAdminRole);
    }
}
