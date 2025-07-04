<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/reset-password', function (Request $request) {
    $token = $request->query('token');
    $email = $request->query('email');

    return view('auth.reset-password', compact('token', 'email'));
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
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

        // Kirim email pemberitahuan
        Mail::raw('Password Anda berhasil diubah.', function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Password Berhasil Diubah - Masjid UIKA');
        });

        return view('auth.reset-password-success');
    }

    return back()->withErrors(['email' => [__($status)]]);
})->name('password.update');
