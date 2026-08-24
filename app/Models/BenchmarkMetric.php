<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BenchmarkMetric extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'benchmark_run_id',
        'latency_ms',
        'throughput_ops_per_sec',
        'energy_joules_per_op',
        'f1_score',
        'false_positive_rate',
        'accuracy',
        'precision_score',
        'recall',
        'roc_auc',
        'memory_mb',
        'cpu_utilization',
        'gpu_utilization',
        'roc_curve',
    ];

    protected $casts = [
        'latency_ms' => 'decimal:18',
        'throughput_ops_per_sec' => 'decimal:8',
        'energy_joules_per_op' => 'decimal:20',
        'f1_score' => 'decimal:15',
        'false_positive_rate' => 'decimal:15',
        'accuracy' => 'decimal:15',
        'precision_score' => 'decimal:15',
        'recall' => 'decimal:15',
        'roc_auc' => 'decimal:15',
        'memory_mb' => 'decimal:8',
        'cpu_utilization' => 'decimal:4',
        'gpu_utilization' => 'decimal:4',
        'roc_curve' => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(BenchmarkRun::class, 'benchmark_run_id');
    }
}
