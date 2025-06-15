<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    //

    public function index()
    {
        $doctors = User::where('role', 'doctor')->with('clinic', 'specialization')->get();
        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'clinic_id' => 'nullable|exists:clinics,id',
            'specialization_id' => 'nullable|exists:specializations,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'certification' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data['password'] = bcrypt($data['password']);
        $doctor = User::create($data);

        if($request->hasFile('photo')){
            $image = $request->file('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = $image->storeAs('doctors', $imageName, 'public');
            $doctor->photo = '/storage/' . $filePath;
            $doctor->save();
        }
        return response()->json([
            'status' => 'success',
            'data' => $doctor
        ],201);
    }

    // update
    public function update(Request $request, $id)
    {
        $doctor = User::findOrFail($id);

        $data = request()->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $doctor->id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string',
            'clinic_id' => 'nullable|exists:clinics,id',
            'specialization_id' => 'nullable|exists:specializations,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'certification' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if(isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $doctor->update($data);

        if($request->hasFile('photo')){
            $image = $request->file('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $filePath = $image->storeAs('doctors', $imageName, 'public');
            $doctor->photo = '/storage/' . $filePath;
            $doctor->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $doctor
        ]);
    }
    public function destroy($id)
    {
        $doctor = User::findOrFail($id);
        $doctor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Doctor deleted successfully'
        ]);
    }

    // showDoctorActive
    public function getDoctorActive()
    {
        $doctors = User::where('role', 'doctor')
            ->where('status', 'active')
            ->with('clinic', 'specialization')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    // get search doctor
    public function searchDoctor(Request $request)
    {
        $doctors = User::where('role', 'doctor')
            ->where('name', 'like', '%' . $request->name . '%')
            ->orWhere('specialization_id',$request->specialization_id)
            ->orWhere('clinic_id', $request->clinic_id)
            ->with('clinic', 'specialization')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

}
