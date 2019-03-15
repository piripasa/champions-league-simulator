<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = [
        'team_id',
        'points',
        'played',
        'won',
        'drawn',
        'lost',
        'goal_difference'
    ];
}
