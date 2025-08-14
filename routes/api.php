<?php

use App\Http\Controllers\Api\AgoraCallController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FirebaseAuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SpecializationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('api.login');
    Route::post('/login/google', [FirebaseAuthController::class, 'login']);
});

Route::post('/register', [UserController::class, 'store'])->name('api.user.register');
Route::post('/logout',[UserController::class, 'logout'])->name('api.logout');

Route::middleware('auth:sanctum')->group(function () {
    // ✅ USER
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::post('/check', 'check')->name('api.user.check');
        Route::post('/checkemail', 'checkEmail')->name('api.user.checkemail');
        Route::get('/{email}', 'show')->middleware('permission:view patients')->name('api.user.show');
        Route::put('/googleid/{id}', 'updateGoogleId')->middleware('permission:edit patients')->name('api.user.updateGoogleId');
        Route::put('/{id}', 'update')->middleware('permission:edit patients')->name('api.user.update');
        Route::put('/update-token/{id}', 'updateToken')->middleware('permission:edit patients')->name('api.user.updateToken');
        Route::put('/agree-privacy-policy/{id}', 'agreePrivacyPolicy')->middleware('permission:edit patients')->name('api.user.agreePrivacyPolicy');
    });

    // ✅ Specialist
    Route::controller(SpecializationController::class)->prefix('specializations')->group(function () {
        Route::get('/', 'index')->middleware('permission:view specializations')->name('api.specialization.index');
    });

    // ✅ DOCTOR
    Route::controller(DoctorController::class)->prefix('doctors')->group(function () {
        Route::get('/', 'index')->middleware('permission:view doctors')->name('api.doctor.index');
        Route::post('/', 'store')->middleware('permission:create doctors')->name('api.doctor.store');
        Route::put('/{id}', 'update')->middleware('permission:edit doctors')->name('api.doctor.update');
        Route::delete('/{id}', 'destroy')->middleware('permission:delete doctors')->name('api.doctor.destroy');

        Route::get('/active', 'getDoctorActive')->middleware('permission:view doctors')->name('api.doctor.showActive');
        Route::get('/specialist/{specialist_id}' ,'getDoctorBySpecialist')->middleware('permission:view doctors')->name('api.doctor.specialist');
        Route::get('/search', 'searchDoctor')->middleware('permission:view doctors')->name('api.doctor.search');
    });

    // ✅ ORDER
    Route::controller(OrderController::class)->prefix('orders')->group(function () {
        Route::post('/', 'store')->middleware('permission:create transactions')->name('api.order.store');
        Route::get('/', 'index')->middleware('permission:view transactions')->name('api.order.index');
        Route::get('/patient/{patient_id}', 'getOrderByPatient')->middleware('permission:view transactions')->name('api.order.byPatient');
        Route::get('/doctor/{doctor_id}', 'getOrderByDoctor')->middleware('permission:view transactions')->name('api.order.byDoctor');
        Route::get('/clinic/{clinic_id}', 'getOrderByClinic')->middleware('permission:view transactions')->name('api.order.byClinic');
        Route::get('/summary/{clinic_id}', 'getSummary')->middleware('permission:view transactions')->name('api.order.summary');
        Route::post('/xendit-callback', [OrderController::class, 'handleCallback'])
        ->withoutMiddleware(['auth:sanctum', 'permission'])
        ->name('api.order.xenditCallback');

        Route::get('/doctor/{doctor_id}/{service}/{status_service}', [OrderController::class, 'getOrderByDoctorQuery'])->middleware('auth:sanctum');

        });


    // AGORA
    Route::controller(AgoraCallController::class)->prefix('agora')->group(function () {
        Route::get('/calls', 'index')->middleware('permission:view agora calls')->name('api.agora.calls.index');
        Route::post('/calls', 'store')->middleware('permission:create agora calls')->name('api.agora.calls.store');
        Route::get('/generate/{channelId}', 'generate')->middleware('permission:view agora calls')->name('api.agora.generateToken');
    });

});

