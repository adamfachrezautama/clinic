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
            'patient_id' => 'required',
            'doctor_id' => 'required', // Diperbaiki
            'channel_name' => 'required',
            'call_id' => 'required',
        ]);

        $agoraCall = AgoraCall::create($request->only([
            'patient_id',
            'doctor_id',
            'channel_name',
            'call_id',
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Agora call added successfully',
            'data' => $agoraCall,
        ], 201);
    }

    public function generate($channelId)
    {
        $appId = env('AGORA_APP_ID'); // Diperbaiki
        $appCertificate = env('AGORA_APP_CERTIFICATE');
        $uid = rand(1, 23000);
        $expirationTimeInSeconds = 864000;
        $currentTimeStamp = time();
        $privilegeExpiredTs = $currentTimeStamp + $expirationTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid(
            $appId,
            $appCertificate,
            $channelId,
            $uid,
            RtcTokenBuilder::RolePublisher,
            $privilegeExpiredTs
        );

        return response()->json([
            'token' => $token,
            'uid' => $uid
        ]);
    }
}
