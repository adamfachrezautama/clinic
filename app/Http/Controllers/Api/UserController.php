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
        return response()->json([
            'status' => 'success',
             'user' => $request->user()]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
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


            return response()->json(['status' => 'success',
                'data' => $user
        ]);

            return response()->json([
                'status' => 'error',
                'message' => 'User not found',

        ], 404);


        return response()->json(['status' => 'success', 'data' => $user]);

    }

    public function update(UpdateUserRequest $request, $id)
    {
        $auth = auth()->user();
        $user = User::findOrFail($id);
        if(!$auth->hasRole('admin') && $auth->id !== $user->id){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to update this user',
            ], 403);
        }
        $data = $request->validated();

        $this->userService->update($user, $data);

        if ($request->hasFile('photo')) {
            $this->userService->uploadPhoto($user, $request->file('photo'));
        }

      return response()->json([
        'status' => 'success',
        'data' => [
            'user' => $user,
        ]
      ],200);
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
