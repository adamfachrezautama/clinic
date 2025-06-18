<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
  // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = ['web', 'api']; // Specify the guards you want to use

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
            foreach ($guard as $g) {
                // Create permission for each guard
                Permission::firstOrCreate([
                    'name' => $perm,
                     'guard_name' => $g]);
            }
        }

        // === Roles ===
        $admin = Role::firstOrCreate(['name' => 'admin','guard_name' => 'web']);
        $doctor = Role::firstOrCreate(['name' => 'doctor','guard_name' => 'api']);
        $patient = Role::firstOrCreate(['name' => 'patient','guard_name' => 'api']);

        // === Assign permissions to roles ===
        $admin->syncPermissions(Permission::where('guard_name','web')->get()); // semua


        $doctor->syncPermissions(Permission::where('guard_name','api')->whereIn('name',[
            'view users',
            'view doctors',
            'create doctors',
            'edit doctors',
        ])->get());

        $patient->syncPermissions(Permission::where('guard_name','api')->whereIn('name',[
            'view doctors',
            'create users', // Assuming patients can create their own user profile
            'edit users', // Assuming patients can edit their own user profile
            'view users', // Assuming patients can view their own user profile
        ])->get());

        // === Optional: Assign admin role to specific user ===
        $adminUser = User::where('email', 'admin@mail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($admin);
        }

         // === Assign doctor + patient roles to doctor user ===
        $doctorUser = User::where('email', 'doctor1@mail.com')->first();
        if ($doctorUser) {
            $doctorUser->syncRoles([
                Role::findByName('doctor', 'api'),
                Role::findByName('patient', 'api'),
            ]);
        }

        // === Assign patient role to patient user ===
        $patientUser = User::where('email', 'patient1@mail.com')->first();
        if ($patientUser) {
            $patientUser->syncRoles([
                Role::findByName('patient', 'api'),
            ]);
        }
    }
}
