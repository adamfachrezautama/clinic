<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('api.login');
});

Route::middleware('auth:sanctum')->group(function () {

    // ✅ USER
    Route::controller(UserController::class)->group(function () {
        Route::post('/logout', 'logout')->name('api.logout');
        Route::post('/user/check', 'check')->name('api.user.check');
        Route::post('/user/checkemail', 'checkEmail')->name('api.user.checkemail');

        Route::post('/user', 'store')->middleware('permission:create users')->name('api.user.store');
        Route::get('/user/{email}', 'show')->middleware('permission:view users')->name('api.user.show');
        Route::put('/user/googleid/{id}', 'updateGoogleId')->middleware('permission:edit users')->name('api.user.updateGoogleId');
        Route::put('/user/{id}', 'update')->middleware('permission:edit users')->name('api.user.update');
    });

    // ✅ DOCTOR
    Route::controller(DoctorController::class)->prefix('doctors')->group(function () {
        Route::get('/', 'index')->middleware('permission:view doctors')->name('api.doctor.index');
        Route::post('/', 'store')->middleware('permission:create doctors')->name('api.doctor.store');
        Route::get('/{id}', 'show')->middleware('permission:view doctors')->name('api.doctor.show');
        Route::put('/{id}', 'update')->middleware('permission:edit doctors')->name('api.doctor.update');
        Route::delete('/{id}', 'destroy')->middleware('permission:delete doctors')->name('api.doctor.destroy');

        Route::get('/active', 'showDoctorActive')->middleware('permission:view doctors')->name('api.doctor.showActive');
        Route::get('/search', 'searchDoctor')->middleware('permission:view doctors')->name('api.doctor.search');
    });

    // ✅ ORDER
    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::post('/', 'store')->name('api.order.store');
        Route::get('/', 'index')->name('api.order.index');
        Route::get('/patient/{patient_id}', 'getOrderByPatient')->name('api.order.byPatient');
        Route::get('/doctor/{doctor_id}', 'getOrderByDoctor')->name('api.order.byDoctor');
        Route::get('/clinic/{clinic_id}', 'getOrderByClinic')->name('api.order.byClinic');
        Route::get('/summary/{clinic_id}', 'getSummary')->name('api.order.summary');
        Route::post('/xendit-callback', 'handleCallback')->name('api.order.xenditCallback');
    });

});

