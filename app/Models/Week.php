<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    protected $fillable = [
        'name',
        'season_id'
    ];
}
