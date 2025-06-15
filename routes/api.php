<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// login
Route::post('/login', UserController::class . '@login')
    ->name('api.login');

//user check
Route::post('/user/checkemail', UserController::class . '@checkEmail')
    ->name('api.user.checkemail')
    ->middleware('auth:sanctum');

    // ========================
    // 🧑‍💼 USER ROUTES
    // ========================
    Route::post('/logout', [UserController::class, 'logout'])->name('api.logout');

    Route::post('/user/check', [UserController::class, 'check'])->name('api.user.check');

    Route::post('/user', [UserController::class, 'store'])
        ->middleware('permission:create users')
        ->name('api.user.store');

    // get user
    Route::get('/user/{email}', UserController::class . '@index')
        ->name('api.user.index')
        ->middleware('auth:sanctum');

    // update google id
    Route::put('/user/googleid/{id}', UserController::class . '@updateGoogleId')
        ->name('api.user.updateGoogleId')
        ->middleware('auth:sanctum');

    // update user
    Route::put('/user/{id}', UserController::class . '@update')
        ->name('api.user.update')
        ->middleware('auth:sanctum');


    // doctor

    //get all doctors
    Route::get('/doctors', DoctorController::class. '@index');

    // store doctor
    Route::post('/doctors', DoctorController::class . '@store')
        ->name('api.doctor.store')
        ->middleware('auth:sanctum');
    // update doctor
    Route::put('/doctors/{id}', DoctorController::class . '@update')
        ->name('api.doctor.update')
        ->middleware('auth:sanctum');
    // delete doctor
    Route::delete('/doctors/{id}', DoctorController::class . '@destroy')
        ->name('api.doctor.destroy')
        ->middleware('auth:sanctum');
    // get active doctors
    Route::get('/doctors/active', DoctorController::class . '@showDoctorActive')
        ->name('api.doctor.showDoctorActive')
        ->middleware('auth:sanctum');
    // search doctor
    Route::get('/doctors/search', DoctorController::class . '@searchDoctor')
        ->name('api.doctor.searchDoctor')
        ->middleware('auth:sanctum');


// orders

//store order
Route::post('/orders', [OrderController::class, 'store'])->middleware('auth:sanctum');

//get order by patient
Route::get('/orders/patient/{patient_id}', [OrderController::class, 'getOrderByPatient'])->middleware('auth:sanctum');

//get order by doctor
Route::get('/orders/doctor/{doctor_id}', [OrderController::class, 'getOrderByDoctor'])->middleware('auth:sanctum');

//get all order
Route::get('/orders', [OrderController::class, 'index'])->middleware('auth:sanctum');

//get order by clinic
Route::get('/orders/clinic/{clinic_id}', [OrderController::class, 'getOrderByClinic'])->middleware('auth:sanctum');

//get clinic summary
Route::get('orders/summary/{clinic_id}', [OrderController::class, 'getSummary'])->middleware('auth:sanctum');

//xendit callback
Route::post('/xendit-callback', [OrderController::class, 'handleCallback']);
