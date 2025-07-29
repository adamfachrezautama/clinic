<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpecializationController extends Controller
{
    //

    public function index()
    {
        $specializations = \App\Models\Specialization::all();
        return response()->json([
            'status' => 'success',
            'data' => $specializations
        ]);
    }
}
