<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Enums\PermissionEnum;

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
        foreach (PermissionEnum::cases() as $perm) {
            foreach ($guard as $g) {
                Permission::firstOrCreate(['name' => $perm->value, 'guard_name' => $g]);
            }
        }

        // === Roles ===
        $adminWeb = Role::firstOrCreate(['name' => 'admin','guard_name' => 'web']);
        $adminApi = Role::firstOrCreate(['name' => 'admin','guard_name' => 'api']);
        $doctor = Role::firstOrCreate(['name' => 'doctor','guard_name' => 'api']);
        $patient = Role::firstOrCreate(['name' => 'patient','guard_name' => 'api']);

        // === Assign permissions to roles ===
        $adminWeb->syncPermissions(Permission::where('guard_name','web')->get()); // semua
        $adminApi->syncPermissions(Permission::where('guard_name','api')->get()); // semua


       $doctor->syncPermissions(
         Permission::where('guard_name', 'api')
            ->whereIn('name', PermissionEnum::toValues(PermissionEnum::doctorRolePermissions()))
            ->get()
            );

        $patient->syncPermissions(
            Permission::where('guard_name', 'api')
                ->whereIn('name', PermissionEnum::toValues(PermissionEnum::patientRolePermissions()))
                ->get()
        );

        // === Optional: Assign admin role to specific user ===
        $adminUser = User::where('email', 'admin@mail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($adminWeb, $adminApi);
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
