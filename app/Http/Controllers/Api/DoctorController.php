<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\User;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Spatie\Permission\Contracts\Role;

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


    // getDoctorActive
    public function getDoctorActive()

    {
        $doctors = User::role('doctor')
            ->where('status', 'online')
            ->with(['clinic', 'specialization'])
            ->get();

        return response()->json(['status' => 'success', 'data' => $doctors]);
    }

    //getDoctorBySpecialist
    public function getDoctorBySpecialist($specialist_id)
       {
        $doctors = User::role('doctor')->where('specialist_id', $specialist_id)->with('clinic', 'specialization')->get();
        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    public function searchDoctor(Request $request)
    {
        $query = User::role('doctor');
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

     public function getClinicById($id)
    {
        $clinic = \App\Models\Clinic::find($id);
        $clinicName = $clinic->name;
        $clinicImage = $clinic->image;
        $totalDoctor = User::where('clinic_id', $id)->count();
        $totalPatient = \App\Models\Order::where('clinic_id', $id)->count();
        $totalIncome = \App\Models\Order::where('clinic_id', $id)->where('status', 'success')->sum('price');
        return response()->json([
            'status' => 'success',
            'data' => [
                'clinic_name' => $clinicName,
                'total_doctor' => $totalDoctor,
                'total_patient' => $totalPatient,
                'clinic_image' => $clinicImage,
                'total_income' => $totalIncome
            ]
        ]);
    }
}
