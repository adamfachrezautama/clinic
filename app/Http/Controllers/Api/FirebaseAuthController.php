<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\User;
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
                return response()->json(['error' => 'ID token kosong'], 400);
            }

            // Verifikasi ID token Firebase
            $verifiedIdToken = $this->auth->verifyIdToken($idTokenString);
            $claims = $verifiedIdToken->claims()->all();

            // Ambil data dari token
            $uid = $claims['user_id'] ?? null;
            $name = $claims['name'] ?? null;
            $email = $claims['email'] ?? null;
            $photo = $claims['picture'] ?? null;
            $emailVerified = $claims['email_verified'] ?? false;

            if (!$email || !$uid) {
                return response()->json(['error' => 'Data tidak lengkap dari token'], 422);
            }

            if (!$emailVerified) {
                return response()->json(['error' => 'Email belum diverifikasi'], 403);
            }

            // Cek apakah user sudah ada
            $user = User::where('email', $email)->first();

            if ($user) {
                // Update info jika user sudah ada
                $user->update([
                    'google_id' => $user->google_id ?? $uid,
                    'name' => $user->name ?? $name,
                    'photo' => $photo,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                    'status' => 'online',
                ]);
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $name ?? explode('@', $email)[0],
                    'email' => $email,
                    'google_id' => $uid,
                    'email_verified_at' => now(),
                    'photo' => $photo,
                    'password' => Hash::make(bin2hex(random_bytes(16))),
                    'status' => 'online',
                    'role' => 'user', // default role jika belum ada
                ]);
                $user->assignRole('patient');
            }

            // Generate token Sanctum
            $token = $user->createToken('firebase_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'photo' => $user->photo,
                        'role' => $user->role,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Token tidak valid',
                'exception' => $e->getMessage(),
            ], 401);
        }
    }
}
