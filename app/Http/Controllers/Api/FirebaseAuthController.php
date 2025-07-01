<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FirebaseAuthController extends Controller
{
   protected $auth;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
        $this->auth = $factory->createAuth();
    }

    public function login(Request $request)
    {
        try {
            $idTokenString = $request->input('id_token');
            if (!$idTokenString) {
                return response()->json(['error' => 'Token kosong'], 400);
            }
            $verifiedIdToken = $this->auth->verifyIdToken($idTokenString);
            $claims = $verifiedIdToken->claims()->all();


            // Ambil data penting dari token
            $uid    = $claims['user_id'];
            $name   = $claims['name'] ?? null;
            $email  = $claims['email'] ?? null;
            $photo  = $claims['picture'] ?? null;
            $emailVerified = $claims['email_verified'] ?? false;

            if (!$email || !$uid) {
                return response()->json(['error' => 'Data tidak lengkap'], 422);
            }

            // Buat atau update user
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'google_id' => $uid,
                    'email_verified_at' => $emailVerified ? now() : null,
                    'photo' => $photo,
                    'password' => Hash::make(bin2hex(random_bytes(16))), // password random
                    'status' => 'online',
                ]
            );

            // Login Laravel dan generate token Sanctum
            $token = $user->createToken('firebase_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Token tidak valid',
                'exception' => $e->getMessage(),
            ], 401);
        }
    }
}
