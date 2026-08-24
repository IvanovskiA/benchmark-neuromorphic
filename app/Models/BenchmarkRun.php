<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BenchmarkRun extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'dataset_id',
        'architecture_id',
        'status',
        'started_at',
        'finished_at',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }

    public function architecture(): BelongsTo
    {
        return $this->belongsTo(Architecture::class);
    }

    public function metric(): HasOne
    {
        return $this->hasOne(BenchmarkMetric::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (BenchmarkRun $run) {
            $run->metric?->delete();
        });
    }
}
