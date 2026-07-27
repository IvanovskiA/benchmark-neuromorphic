<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $columns = [
        'latency_ms',
        'throughput_ops_per_sec',
        'f1_score',
        'false_positive_rate',
    ];

    public function up(): void
    {
        foreach ($this->columns as $column) {
            DB::statement("ALTER TABLE benchmark_metrics ALTER COLUMN {$column} TYPE DOUBLE PRECISION USING {$column}::double precision");
        }
    }

    public function down(): void
    {
        $revertTypes = [
            'latency_ms' => 'NUMERIC(12, 4)',
            'throughput_ops_per_sec' => 'NUMERIC(12, 4)',
            'f1_score' => 'NUMERIC(8, 6)',
            'false_positive_rate' => 'NUMERIC(8, 6)',
        ];

        foreach ($revertTypes as $column => $type) {
            DB::statement("ALTER TABLE benchmark_metrics ALTER COLUMN {$column} TYPE {$type} USING {$column}::{$type}");
        }
    }
};
