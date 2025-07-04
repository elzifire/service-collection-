<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ZakatController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\MualafController;
use Illuminate\Support\Facades\Mail;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::put('/user/{id}', [UserController::class, 'update']);
});

Route::controller(ZakatController::class)->group(function () {
    Route::post('hitung-emas', 'hitungZakatEmas');
    Route::post('hitung-perak', 'hitungZakatPerak');
    Route::post('hitung-perdagangan', 'hitungZakatPerdagangan');
    Route::post('hitung-penghasilan-bulan', 'hitungZakatPenghasilanBulan');
    Route::post('hitung-penghasilan-tahunan', 'hitungZakatPenghasilanTahunan');
});

Route::prefix('donations')->group(function () {
    Route::get('/', [DonationController::class, 'index']);
    Route::get('/{id}', [DonationController::class, 'show']);
    Route::get('/donated', [DonationController::class, 'donated'])->middleware('auth:sanctum');
    Route::post('/', [DonationController::class, 'store']);
    Route::get('/getImage/{id}', [DonationController::class, 'getImage']);
});

Route::post('/mualaf', [MualafController::class, 'store']);

Route::get('/test-email', function () {
    Mail::raw('Test email from Laravel', function ($message) {
        $message->to('test@example.com')
                ->subject('Test Email')
                ->from('zenscilla@gmail.com');
    });
    return 'Email sent!';
});