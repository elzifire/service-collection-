<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private function apiResponse($status, $message, $data = null, $httpCode = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $httpCode);
    }

    // register user
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return $this->apiResponse(
                'success',
                'User berhasil dibuat',
                $user
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->apiResponse(
                'error',
                'Validasi gagal',
                $e->errors(),
                422
            );
        }
    }

    // login user
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->apiResponse(
                    'error',
                    'Email atau password salah',
                    null,
                    401
                );
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->apiResponse(
                'success',
                'Login berhasil',
                [
                    'user' => $user,
                    'token' => $token,
                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->apiResponse(
                'error',
                'Validasi gagal',
                $e->errors(),
                422
            );
        }
    }

    // logout user
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->apiResponse(
                'success',
                'Logout berhasil'
            );
        } catch (\Exception $e) {
            return $this->apiResponse(
                'error',
                'Gagal logout',
                null,
                500
            );
        }
    }

    // forgot password
    public function forgotPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            Log::info('Forgot Password: Attempting to send reset link for email: ' . $validated['email']);

            $status = Password::sendResetLink(
                $validated
            );

            Log::info('Forgot Password: Send reset link status: ' . $status);

            if ($status === Password::RESET_LINK_SENT) {
                return $this->apiResponse(
                    'success',
                    'Link reset password telah dikirim ke email Anda'
                );
            }

            return $this->apiResponse(
                'error',
                'Gagal mengirim link reset password: ' . $status,
                null,
                400
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Forgot Password Validation Error: ' . json_encode($e->errors()));
            return $this->apiResponse(
                'error',
                'Validasi gagal',
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            Log::error('Forgot Password Error: ' . $e->getMessage());
            return $this->apiResponse(
                'error',
                'Terjadi kesalahan saat mengirim link reset: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    // reset password
    public function resetPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $status = Password::reset(
                $validated,
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->apiResponse(
                    'success',
                    'Password berhasil direset'
                );
            }

            return $this->apiResponse(
                'error',
                'Gagal mereset password. Token tidak valid.',
                null,
                400
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->apiResponse(
                'error',
                'Validasi gagal',
                $e->errors(),
                422
            );
        } catch (\Exception $e) {
            return $this->apiResponse(
                'error',
                'Terjadi kesalahan saat mereset password',
                null,
                500
            );
        }
    }
}