<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';
    
    // connect to the database
    protected $connection = 'mualaf';

    // specify the primary key
    protected $primaryKey = 'id';

    protected $guarded = [];
}
