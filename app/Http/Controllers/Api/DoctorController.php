<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\User;
use App\Services\DoctorService;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    protected DoctorService $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function index()
    {
        $doctors = User::where('role', 'doctor')->with('clinic', 'specialization')->get();

        return response()->json(['status' => 'success', 'data' => $doctors]);
    }

    public function store(StoreDoctorRequest $request)
    {
        $data = $request->validated();
        $data['role'] = 'doctor'; // Set default role

        $doctor = $this->doctorService->create($data);

        if ($request->hasFile('photo')) {
            $this->doctorService->uploadPhoto($doctor, $request->file('photo'));
        }

        if ($request->hasFile('certification')) {
            $this->doctorService->uploadCertification($doctor, $request->file('certification'));
        }

        return response()->json(['status' => 'success', 'data' => $doctor], 201);
    }

    public function update(UpdateDoctorRequest $request, $id)
    {
        $doctor = User::findOrFail($id);
        $data = $request->validated();

        $this->doctorService->update($doctor, $data);

        if ($request->hasFile('photo')) {
            $this->doctorService->uploadPhoto($doctor, $request->file('photo'));
        }

        if ($request->hasFile('certification')) {
            $this->doctorService->uploadCertification($doctor, $request->file('certification'));
        }

        return response()->json(['status' => 'success', 'data' => $doctor]);
    }

    public function destroy($id)
    {
        $doctor = User::findOrFail($id);
        $doctor->delete();

        return response()->json(['status' => 'success', 'message' => 'Doctor deleted successfully']);
    }


    // showDoctorActive
    public function getDoctorActive()

    {
        $doctors = User::where('role', 'doctor')->where('status', 'active')->with('clinic', 'specialization')->get();

        return response()->json(['status' => 'success', 'data' => $doctors]);
    }

    public function searchDoctor(Request $request)
    {
        $query = User::where('role', 'doctor');


        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('specialization_id')) {
            $query->where('specialization_id', $request->specialization_id);
        }

        if ($request->filled('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        $doctors = $query->with('clinic', 'specialization')->get();
        $doctors = User::where('role', 'doctor')
            ->where('name', 'like', '%' . $request->name . '%')
            ->orWhere('specialization_id',$request->specialization_id)
            ->orWhere('clinic_id', $request->clinic_id)
            ->with('clinic', 'specialization')
            ->get();



        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('specialization_id')) {
            $query->where('specialization_id', $request->specialization_id);
        }

        if ($request->filled('clinic_id')) {
            $query->where('clinic_id', $request->clinic_id);
        }

        $doctors = $query->with('clinic', 'specialization')->get();


        return response()->json(['status' => 'success', 'data' => $doctors]);
    }
}
