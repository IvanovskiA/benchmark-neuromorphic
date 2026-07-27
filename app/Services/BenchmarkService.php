<?php

namespace App\Services;

use App\Models\BenchmarkRun;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class BenchmarkService
{
    public function run(BenchmarkRun $run): void
    {
        $run->update([
            'status' => 'running',
            'started_at' => now(),
            'error_message' => null,
        ]);

        $result = Process::timeout(config('benchmark.timeout'))
            ->env([
                'DB_HOST' => config('database.connections.pgsql.host'),
                'DB_PORT' => config('database.connections.pgsql.port'),
                'DB_DATABASE' => config('database.connections.pgsql.database'),
                'DB_USERNAME' => config('database.connections.pgsql.username'),
                'DB_PASSWORD' => config('database.connections.pgsql.password'),
                'CICIDS_PATH' => config('benchmark.cicids_path'),
                'UNSW_NB15_PATH' => config('benchmark.unsw_nb15_path'),
            ])
            ->run([
                config('benchmark.python_path'),
                config('benchmark.script_path'),
                '--run-id', $run->id,
            ]);

        if ($result->failed()) {
            $run->update([
                'status' => 'failed',
                'finished_at' => now(),
                'error_message' => trim($result->errorOutput() ?: $result->output()) ?: 'Python benchmark failed.',
            ]);

            throw new RuntimeException($run->error_message);
        }

        $run->refresh();

        if ($run->status !== 'completed') {
            $run->update([
                'status' => 'completed',
                'finished_at' => now(),
            ]);
        }
    }
}
