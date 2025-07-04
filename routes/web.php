<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DonationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/donations', [DonationController::class, 'test']);

Route::get('/reset-password', function (Illuminate\Http\Request $request) {
    $token = $request->query('token');
    $email = $request->query('email');

    return view('auth.reset-password', compact('token', 'email'));
})->name('password.reset');

use Illuminate\Support\Facades\Password;

Route::post('/reset-password', function (Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:6',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password)
            ])->save();
        }
    );

    if ($status == Password::PASSWORD_RESET) {
        return redirect('/login')->with('status', __($status));
    }

    return back()->withErrors(['email' => [__($status)]]);
})->name('password.update');
