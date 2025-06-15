<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class DoctorService
{
    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function update(User $doctor, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $doctor->update($data);
        return $doctor;
    }

    public function uploadPhoto(User $doctor, $photo)
    {
        if ($doctor->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $doctor->photo))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $doctor->photo));
        }

        $imageName = time() . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('doctors', $imageName, 'public');
        $doctor->photo = '/storage/' . $path;
        $doctor->save();
    }

    public function deletePhoto(User $doctor)
    {
        if ($doctor->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $doctor->photo))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $doctor->photo));
        }
    }

    public function uploadCertification(User $doctor, $cert)
    {
        if ($doctor->certification && Storage::disk('public')->exists(str_replace('/storage/', '', $doctor->certification))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $doctor->certification));
    }

        $imageName = time() . '_cert.' . $cert->getClientOriginalExtension();
        $path = $cert->storeAs('doctors/certifications', $imageName, 'public');
        $doctor->certification = '/storage/' . $path;
        $doctor->save();
    }

}
