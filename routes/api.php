<?php

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

    // ========================
    // 🧑‍💼 USER ROUTES
    // ========================
    Route::post('/logout', [UserController::class, 'logout'])->name('api.logout');

    Route::post('/user/check', [UserController::class, 'check'])->name('api.user.check');

    Route::post('/user', [UserController::class, 'store'])
        ->middleware('permission:create users')
        ->name('api.user.store');

    Route::get('/user/{email}', [UserController::class, 'show'])
        ->middleware('permission:view users')
        ->name('api.user.show');

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

    Route::get('doctors-active', [DoctorController::class, 'getDoctorActive'])
        ->middleware('permission:view doctors');

    Route::get('doctors-search', [DoctorController::class, 'searchDoctor'])
        ->middleware('permission:view doctors');

});
