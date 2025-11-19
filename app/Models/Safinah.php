<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Safinah extends Model
{
    protected $connection = 'safinah';

    protected $table = 'contents';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'arabic',
        'indonesia'
    ];
    
}

