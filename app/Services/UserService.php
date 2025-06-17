<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


class UserService
{
    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $user->assignRole('patient'); // Default role for new users on table model_has_roles

        return $user;

    }

   public function update(User $user, array $data)

    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
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
