<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = [
        'name',
        'completed',
        'completed_at'
    ];
}
