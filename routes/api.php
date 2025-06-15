<?php

<<<<<<< HEAD
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
=======
>>>>>>> f1d5cb21c242a3f53df081922a535f2bac30db29
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DoctorController;

/*
|--------------------------------------------------------------------------
| Public Routes (Tanpa Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('api.login');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Butuh Login & Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

<<<<<<< HEAD
//user check
Route::post('/user/checkemail', UserController::class . '@checkEmail')
    ->name('api.user.checkemail')
    ->middleware('auth:sanctum');

    // ========================
    // 🧑‍💼 USER ROUTES
    // ========================
    Route::post('/logout', [UserController::class, 'logout'])->name('api.logout');

    Route::post('/user/check', [UserController::class, 'check'])->name('api.user.check');

=======
    // ========================
    // 🧑‍💼 USER ROUTES
    // ========================
    Route::post('/logout', [UserController::class, 'logout'])->name('api.logout');

    Route::post('/user/check', [UserController::class, 'check'])->name('api.user.check');

>>>>>>> f1d5cb21c242a3f53df081922a535f2bac30db29
    Route::post('/user', [UserController::class, 'store'])
        ->middleware('permission:create users')
        ->name('api.user.store');

<<<<<<< HEAD
    // get user
    Route::get('/user/{email}', UserController::class . '@index')
        ->name('api.user.index')
        ->middleware('auth:sanctum');
=======
    Route::get('/user/{email}', [UserController::class, 'show'])
        ->middleware('permission:view users')
        ->name('api.user.show');
>>>>>>> f1d5cb21c242a3f53df081922a535f2bac30db29

    Route::put('/user/googleid/{id}', [UserController::class, 'updateGoogleId'])
        ->middleware('permission:edit users')
        ->name('api.user.updateGoogleId');

    Route::put('/user/{id}', [UserController::class, 'update'])
        ->middleware('permission:edit users')
        ->name('api.user.update');

    // ========================
    // 👨‍⚕️ DOCTOR ROUTES
    // ========================

    Route::apiResource('doctors', DoctorController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy'])
        ->middleware([
            'permission:view doctors|create doctors|edit doctors|delete doctors'
        ]);

<<<<<<< HEAD
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
=======
    Route::get('doctors-active', [DoctorController::class, 'getDoctorActive'])
        ->middleware('permission:view doctors');

    Route::get('doctors-search', [DoctorController::class, 'searchDoctor'])
        ->middleware('permission:view doctors');

});
>>>>>>> f1d5cb21c242a3f53df081922a535f2bac30db29
