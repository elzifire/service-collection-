<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    // Menggunakan koneksi donasi (default)
    protected $connection = 'donasi';
    protected $table = 'donations';
    protected $fillable = [
        'user_id',
        'campaign_id',
        'status_id',
        'amount',
        'proof_image',
    ];

    // Relasi ke User; karena User model telah disetting koneksi 'mysql',
    // maka relasi ini akan mengambil data dari koneksi mysql.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
