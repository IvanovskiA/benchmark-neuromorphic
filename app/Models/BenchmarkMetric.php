<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenchmarkMetric extends Model
{
    protected $fillable = [
        'benchmark_run_id',
        'latency_ms',
        'throughput_ops_per_sec',
        'energy_joules_per_op',
        'f1_score',
        'false_positive_rate',
    ];

    protected $casts = [
        'latency_ms' => 'decimal:18',
        'throughput_ops_per_sec' => 'decimal:8',
        'energy_joules_per_op' => 'decimal:20',
        'f1_score' => 'decimal:15',
        'false_positive_rate' => 'decimal:15',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(BenchmarkRun::class, 'benchmark_run_id');
    }
}
