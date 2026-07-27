<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE benchmark_metrics ALTER COLUMN energy_joules_per_op TYPE DOUBLE PRECISION USING energy_joules_per_op::double precision');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE benchmark_metrics ALTER COLUMN energy_joules_per_op TYPE NUMERIC(12, 8) USING energy_joules_per_op::numeric(12, 8)');
    }
};
