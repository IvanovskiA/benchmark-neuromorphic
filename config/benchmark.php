<?php

return [
    'python_path' => env('BENCHMARK_PYTHON', '/usr/local/bin/python3'),
    'script_path' => env('BENCHMARK_SCRIPT', base_path('python/cli/run_benchmark.py')),
    'timeout' => (int) env('BENCHMARK_TIMEOUT', 3600),
    'cicids_path' => env('CICIDS_PATH', storage_path('datasets/cicids.csv')),
    'unsw_nb15_path' => env('UNSW_NB15_PATH', storage_path('datasets/unsw_nb15.csv')),
];
