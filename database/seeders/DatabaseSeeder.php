<?php

namespace Database\Seeders;

use App\Models\Architecture;
use App\Models\Dataset;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $datasets = [
            ['slug' => 'cicids', 'name' => 'CICIDS'],
            ['slug' => 'unsw_nb15', 'name' => 'UNSW-NB15'],
        ];

        foreach ($datasets as $dataset) {
            Dataset::updateOrCreate(['slug' => $dataset['slug']], $dataset);
        }

        $architectures = [
            ['slug' => 'lava_loihi', 'name' => 'Intel Lava / Loihi', 'is_neuromorphic' => true],
            ['slug' => 'ibm_nscs', 'name' => 'IBM NSCS / TrueNorth', 'is_neuromorphic' => true],
            ['slug' => 'cpu_baseline', 'name' => 'CPU Baseline', 'is_neuromorphic' => false],
            ['slug' => 'gpu_baseline', 'name' => 'GPU Baseline', 'is_neuromorphic' => false],
            ['slug' => 'mnsim_imc', 'name' => 'MNSIM IMC', 'is_neuromorphic' => false],
        ];

        foreach ($architectures as $architecture) {
            Architecture::updateOrCreate(['slug' => $architecture['slug']], $architecture);
        }
    }
}
