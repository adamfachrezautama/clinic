<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AgoraCall;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;

class AgoraCallController extends Controller
{
    //
    public function index()
    {
        $agoraCalls = AgoraCall::all();
        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => $agoraCalls
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'channel_name' => 'required|string|max:255',
            'call_id' => 'required|string|unique:agora_calls,call_id',
        ]);

            $data = $request->all();
            $patient_id = $data['patient_id'];
            $doctor_id = $data['doctor_id'];
            $channel_name = $data['channel_name'];
            $call_id = $data['call_id'];
            $agoraCall = AgoraCall::create([
                'patient_id' => $patient_id,
                'doctor_id' => $doctor_id,
                'channel_name' => $channel_name,
                'call_id' => $call_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Agora call added successfully',
            'data' => $agoraCall,
        ], 201);
    }

    public function generate(Request $request, $channelId)
    {
        $request->validate([
            'role' => 'nullable|in:publisher,subscriber'
        ]);

        $appId = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');

        if (!$appId || !$appCertificate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Agora credentials not set'
            ], 500);
        }

        $uid = auth()->id() ?? rand(1, 999999);
        $expirationTimeInSeconds = 3600; // 1 jam
        $privilegeExpiredTs = time() + $expirationTimeInSeconds;

        $role = $request->input('role') === 'subscriber'
            ? RtcTokenBuilder::RoleSubscriber
            : RtcTokenBuilder::RolePublisher;

        $token = RtcTokenBuilder::buildTokenWithUid(
            $appId,
            $appCertificate,
            $channelId,
            $uid,
            $role,
            $privilegeExpiredTs
        );

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'uid' => $uid,
            'role' => $role == RtcTokenBuilder::RolePublisher ? 'publisher' : 'subscriber',
            'expired_at' => $privilegeExpiredTs
        ]);
    }

}
