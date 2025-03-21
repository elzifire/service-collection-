<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    

    public function index()
    {
        $user = User::all();

        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data User Berhasil Diambil',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data User Gagal Diambil',
                'data' => null
            ], 400);
        }
    }

    public function show($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data User Berhasil Diambil',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data User Gagal Diambil',
                'data' => null
            ], 400);
        }
    }
}
