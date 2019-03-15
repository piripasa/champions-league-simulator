<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strength extends Model
{
    protected $fillable = [
        'team_id',
        'is_home',
        'strength'
    ];
}
