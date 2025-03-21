<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use App\Http\Controllers\Api\ZakatController;
use App\Http\Controllers\Api\DonationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/user', [User::class, 'index']);
    Route::get('/user/{id}', [User::class, 'show']);
});
Route::controller(ZakatController::class)->group(function () {
    Route::post('hitung-emas', 'hitungZakatEmas');
    Route::post('hitung-perak', 'hitungZakatPerak');
    Route::post('hitung-perdagangan', 'hitungZakatPerdagangan');
});



Route::prefix('donations')->group(function () {
    Route::get('/', [DonationController::class, 'index']);
    Route::get('/{id}', [DonationController::class, 'show']);
    // Route::post('/', [DonationController::class, 'store'])->middleware('auth:sanctum');
    // Route::post('/', function (Request $request) {
    // })->middleware('auth:sanctum');
    Route::post('/', [DonationController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/ShowDonationsByUser', [DonationController::class, 'showDonationsByUser'])->middleware('auth:sanctum');
    Route::get('/getImage/{id}', [DonationController::class, 'getImage']);
});