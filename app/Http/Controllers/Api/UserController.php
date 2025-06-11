<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function index($email)
    {
        // Assuming you have a User model and a method to find by email
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }


     // Find the user by ID
    public function updateGoogleId(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'google_id' => 'required|string',
        ]);

        $user = User::find($id);

        if($user){
            $user->google_id = $request->google_id;
            $user->save();

            return response()->json(['status' => 'success',
                'data' => $user
        ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',

        ], 404);
        }
    }

    //update user
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'phone' => 'required',
            'address' => 'nullable|string|max:255',
            'google_id' => 'nullable|string',
            'ktp_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'password' => 'nullable|string|min:8|confirmed',
            'gender' => 'nullable|string',
        ]);

         $email = $request->email;
        $password = $request->password;
         $user = User::where('email', $email)->first();

        if(!$user || Hash::check($password, $user->password)){
            return response()->json(['status' => 'error',
            'message' => 'email or password is incorrect'], 401);
        }

      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'status' => 'success',
        'data' => [
            'user' => $user,
            'token' => $token
        ]
      ],200);
    }

    //logout
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    // store
    public function store(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required',
        ]);
        $data = $request->all();
        $name = $request->name;
        $email = $request->email;
        $password = Hash::make($request->password);
        $role = $request->role ?? 'patient';
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 201);

    }
}
