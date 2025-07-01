<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/test-firebase-path', function () {
//     $path = (base_path(env('FIREBASE_CREDENTIALS')));

//     return [
//         'full_path' => $path,
//         'file_exists' => file_exists($path),
//         'is_readable' => is_readable($path),
//         'content' => file_exists($path) ? file_get_contents($path) : 'N/A',
//     ];
// });

