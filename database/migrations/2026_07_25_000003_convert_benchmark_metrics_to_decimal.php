<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<string, string> */
    private array $columns = [
        'latency_ms' => 'NUMERIC(24, 18)',
        'throughput_ops_per_sec' => 'NUMERIC(24, 8)',
        'energy_joules_per_op' => 'NUMERIC(30, 20)',
        'f1_score' => 'NUMERIC(20, 15)',
        'false_positive_rate' => 'NUMERIC(20, 15)',
    ];

    public function up(): void
    {
        foreach ($this->columns as $column => $type) {
            DB::statement("ALTER TABLE benchmark_metrics ALTER COLUMN {$column} TYPE {$type} USING {$column}::{$type}");
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->columns) as $column) {
            DB::statement("ALTER TABLE benchmark_metrics ALTER COLUMN {$column} TYPE DOUBLE PRECISION USING {$column}::double precision");
        }
    }
};
