<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use App\Http\Controllers\Api\ZakatController;
use App\Http\Controllers\Api\DonationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\MualafController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/user', [User::class, 'index']);
    Route::get('/user/{id}', [User::class, 'show']);
    Route::put('/user/{id}', [User::class, 'update']);
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
    Route::post('/', [DonationController::class, 'store']); // Tanpa middleware auth:sanctum
    Route::get('/getImage/{id}', [DonationController::class, 'getImage']);
});

Route::post('/mualaf', [MualafController::class, 'store']);
// ->middleware('throttle:mualaf');

// Route::get('/donatur', function () {
    
// })->middleware('auth:sanctum');
