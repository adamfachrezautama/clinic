<?php

use App\Http\Controllers\Api\DoctorController;
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
Route::post('/user/check', UserController::class . '@check')
    ->name('api.user.check')
    ->middleware('auth:sanctum');

    // logout
    Route::post('/logout', UserController::class . '@logout')
        ->name('api.logout')
        ->middleware('auth:sanctum');

    // store
    Route::post('/user', UserController::class . '@store')
        ->name('api.user.store')
        ->middleware('auth:sanctum');

    // get user
    Route::get('/user/{email}', UserController::class . '@show')
        ->name('api.user.show')
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
    Route::get('/doctors', DoctorController::class, '@index');

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
