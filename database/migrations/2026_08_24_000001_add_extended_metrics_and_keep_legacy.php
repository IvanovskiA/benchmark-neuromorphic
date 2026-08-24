<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('benchmark_metrics', function (Blueprint $table) {
            $table->decimal('accuracy', 20, 15)->nullable();
            $table->decimal('precision_score', 20, 15)->nullable();
            $table->decimal('recall', 20, 15)->nullable();
            $table->decimal('roc_auc', 20, 15)->nullable();
            $table->decimal('memory_mb', 24, 8)->nullable();
            $table->decimal('cpu_utilization', 12, 4)->nullable();
            $table->decimal('gpu_utilization', 12, 4)->nullable();
            $table->json('roc_curve')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('benchmark_metrics', function (Blueprint $table) {
            $table->dropColumn([
                'accuracy',
                'precision_score',
                'recall',
                'roc_auc',
                'memory_mb',
                'cpu_utilization',
                'gpu_utilization',
                'roc_curve',
            ]);
        });
    }
};
