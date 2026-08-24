<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('architectures', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('benchmark_runs', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('benchmark_metrics', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('architectures', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('benchmark_runs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('benchmark_metrics', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
