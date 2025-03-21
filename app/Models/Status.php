<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    // Menggunakan koneksi donasi
    protected $connection = 'donasi';
    protected $table = 'status';
    protected $fillable = ['name'];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
    
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
