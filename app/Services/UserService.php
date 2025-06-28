<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserService
{
    public function create(array $data)
    {

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        // Tentukan role yang akan digunakan
            $roleName = $data['role'] ?? 'patient';

         // Cek apakah role valid
        if (Role::where('name', $roleName)->where('guard_name', 'api')->exists()) {
            $user->assignRole(Role::findByName($roleName, 'api'));
        } else {
            // Assign default role jika role tidak ditemukan
            $user->assignRole('patient');
        }
            return $user;

    }

   public function update(User $user, array $data)

    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']); // jangan update kalau kosong
        }

        $user->update($data);
        return $user;
    }

    public function uploadPhoto(User $user, $photo)
    {
        if ($user->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $user->photo))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->photo));
        }

        $imageName = time() . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('user', $imageName, 'public');
        $user->photo = '/storage/' . $path;
        $user->save();
    }
    public function deletePhoto(User $user)
{
    if ($user->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $user->photo))) {
        Storage::disk('public')->delete(str_replace('/storage/', '', $user->photo));
    }
}

}
