<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json(['status' => 'success', 'token' => $token, 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'success', 'message' => 'Logged out']);
    }

    public function check(Request $request)
    {
        return response()->json(['status' => 'success', 'user' => $request->user()]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['role'] = 'patient'; // default role
        $user = $this->userService->create($data);

        return response()->json(['status' => 'success', 'data' => $user], 201);
    }

    public function show($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        return response()->json(['status' => 'success', 'data' => $user]);
    }

    public function updateGoogleId(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->google_id = $request->google_id;
        $user->save();

<<<<<<< HEAD
            return response()->json(['status' => 'success',
                'data' => $user
        ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',

        ], 404);
        }
=======
        return response()->json(['status' => 'success', 'data' => $user]);
>>>>>>> f1d5cb21c242a3f53df081922a535f2bac30db29
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        $this->userService->update($user, $data);

        if ($request->hasFile('photo')) {
            $this->userService->uploadPhoto($user, $request->file('photo'));
        }

<<<<<<< HEAD
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'status' => 'success',
        'data' => [
            'user' => $user,
            'token' => $token
        ]
      ],200);
    }

    // login
    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['status' => 'error',
                'message' => 'Email or password is incorrect'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 200);
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
    public function store(Request $request)
    {

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

=======
        return response()->json(['status' => 'success', 'data' => $user]);
>>>>>>> f1d5cb21c242a3f53df081922a535f2bac30db29
    }

    // check if email exists
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json(['status' => 'error', 'message' => 'Email already exists'], 409);
        }

        return response()->json(['status' => 'success', 'message' => 'Email is available'], 200);
    }
}
