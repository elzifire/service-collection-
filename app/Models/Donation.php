<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    protected function getProofImageAttribute($value)
    {
        return url('storage/donations/' . $value);
    }

    public function getCreatedAtAttribute($value)
    {
        // return \Carbon\Carbon::parse($value)->format('d-m-Y H:i:s');

        return Carbon::parse($value)->setTimezone(new \DateTimeZone('Asia/Jakarta'))->format('d-m-Y H:i:s');
        // format tanggal dan waktu dalam timezone Asia/Jakarta
        // return Carbon::parse($value)->format('d-m-Y H:i:s');
        // $date = Carbon::parse('2025-03-19T08:44:26.000000Z', 'UTC')->setTimezone('Asia/Jakarta');
        // return $date->format('d-m-Y H:i:s');
    }
}
