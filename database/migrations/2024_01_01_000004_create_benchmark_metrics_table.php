<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benchmark_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('benchmark_run_id')->constrained('benchmark_runs')->cascadeOnDelete();
            $table->decimal('latency_ms', 24, 18)->nullable();
            $table->decimal('throughput_ops_per_sec', 24, 8)->nullable();
            $table->decimal('energy_joules_per_op', 30, 20)->nullable();
            $table->decimal('f1_score', 20, 15)->nullable();
            $table->decimal('false_positive_rate', 20, 15)->nullable();
            $table->timestamps();

            $table->unique('benchmark_run_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benchmark_metrics');
    }
};
