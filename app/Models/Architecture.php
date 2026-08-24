<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Architecture extends Model
{
    use SoftDeletes;
    protected $fillable = ['slug', 'name', 'is_neuromorphic'];

    protected $casts = [
        'is_neuromorphic' => 'boolean',
    ];
}
