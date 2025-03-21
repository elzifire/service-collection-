<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DonationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/donations', [DonationController::class, 'test']);
