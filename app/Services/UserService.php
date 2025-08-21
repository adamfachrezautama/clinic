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
             $roles =Role::findbyName('patient','api');
        $user->syncRoles($roles);// Assign the 'doctor' role to the new
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
        // Hapus foto lama
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $imageName = $photo->hashName(); // Nama otomatis unik
        $path = $photo->storeAs('user/photos', $imageName, 'public');

        $user->photo = $path; // Simpan hanya path relatif misalnya: user/filename.jpg
        $user->save();
    }
    public function deletePhoto(User $user)
    {
        if ($user->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $user->photo))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->photo));
        }
    }

}
