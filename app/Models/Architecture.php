<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Architecture extends Model
{
    protected $fillable = ['slug', 'name', 'is_neuromorphic'];

    protected $casts = [
        'is_neuromorphic' => 'boolean',
    ];
}
