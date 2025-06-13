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
  // Reset cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === Permissions ===
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view doctors',
            'create doctors',
            'edit doctors',
            'delete doctors',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // === Roles ===
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $patient = Role::firstOrCreate(['name' => 'patient']);

        // === Assign permissions to roles ===
        $admin->syncPermissions(Permission::all()); // semua
        $doctor->syncPermissions([
            'view users',
            'view doctors',
        ]);
        $patient->syncPermissions([
            'view doctors',
        ]);

        // === Optional: Assign admin role to specific user ===
        $adminUser = User::where('email', 'admin@mail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($admin);
        }

    }
}
