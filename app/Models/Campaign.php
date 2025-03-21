<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    // Menggunakan koneksi donasi untuk data campaign
    protected $connection = 'donasi';
    protected $table = 'campaigns';
    protected $guarded = [];

    // Jika relasi ke User, pastikan User model sudah disetting ke koneksi mysql
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(CategoriesCampaigns::class, 'category_id');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function totalDonations()
    {
        return $this->donations()->sum('amount');
    }

    public function isCompleted()
    {
        return $this->totalDonations() >= $this->goal_amount;
    }

    public function donors()
    {
        return $this->hasMany(Donation::class); // Asumsi donor berasal dari model Donation
    }
}
