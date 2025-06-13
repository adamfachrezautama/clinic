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

        return response()->json(['status' => 'success', 'data' => $user]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        $this->userService->update($user, $data);

        if ($request->hasFile('photo')) {
            $this->userService->uploadPhoto($user, $request->file('photo'));
        }

        return response()->json(['status' => 'success', 'data' => $user]);
    }
}
