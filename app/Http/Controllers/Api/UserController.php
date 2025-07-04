<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', only: ['update']),
        ];
    }

    private function apiResponse($status, $message, $data = null, $httpCode = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $httpCode);
    }

    public function index()
    {
        try {
            $users = User::query()
                ->select('id', 'name', 'email', 'phone', 'created_at')
                ->get();
            
            return $this->apiResponse(
                'success',
                'Data User Berhasil Diambil',
                $users
            );
        } catch (\Exception $e) {
            return $this->apiResponse(
                'error',
                'Data User Gagal Diambil',
                null,
                500
            );
        }
    }

    public function show($id)
    {
        try {
            $user = User::query()
                ->select('id', 'name', 'email', 'phone', 'created_at')
                ->findOrFail($id);

            return $this->apiResponse(
                'success',
                'Data User Berhasil Diambil',
                $user
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->apiResponse(
                'error',
                'User Tidak Ditemukan',
                null,
                404
            );
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::query()->findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255|min:2',
                'email' => 'sometimes|email|unique:users,email,'.$id,
                'password' => 'sometimes|string|min:8|confirmed',
                'phone' => 'sometimes|nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|max:20',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update(array_filter($validated));

            return $this->apiResponse(
                'success',
                'Data User Berhasil Diperbarui',
                $user->only(['id', 'name', 'email', 'phone', 'updated_at'])
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->apiResponse(
                'error',
                'User Tidak Ditemukan',
                null,
                404
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->apiResponse(
                'error',
                'Validasi Gagal',
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return $this->apiResponse(
                'error',
                'Terjadi Kesalahan Saat Memperbarui Data',
                null,
                500
            );
        }
    }
}