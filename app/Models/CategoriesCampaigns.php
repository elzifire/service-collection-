<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesCampaigns extends Model
{
    use HasFactory;

    // Menggunakan koneksi donasi
    protected $connection = 'donasi';
    protected $table = 'categories_campaign';

    protected $fillable = ['name'];

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'category_id');
    }
}
