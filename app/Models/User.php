<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    // Pastikan User menggunakan koneksi mysql
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
    public function sendPasswordResetNotification($token)
    {
        Log::info('Sending reset password notification for user: ' . $this->email);
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
