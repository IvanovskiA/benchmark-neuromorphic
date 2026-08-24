<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE benchmark_metrics ADD COLUMN created_at_tmp timestamp(0) without time zone');
        DB::statement('ALTER TABLE benchmark_metrics ADD COLUMN updated_at_tmp timestamp(0) without time zone');
        DB::statement('UPDATE benchmark_metrics SET created_at_tmp = created_at, updated_at_tmp = updated_at');
        DB::statement('ALTER TABLE benchmark_metrics DROP COLUMN created_at');
        DB::statement('ALTER TABLE benchmark_metrics DROP COLUMN updated_at');
        DB::statement('ALTER TABLE benchmark_metrics RENAME COLUMN created_at_tmp TO created_at');
        DB::statement('ALTER TABLE benchmark_metrics RENAME COLUMN updated_at_tmp TO updated_at');
    }

    public function down(): void
    {
        // Column order cannot be restored without rebuilding the table.
    }
};
