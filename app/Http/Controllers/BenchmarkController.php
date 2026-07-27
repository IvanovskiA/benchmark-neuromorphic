<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBenchmarkRequest;
use App\Models\Architecture;
use App\Models\BenchmarkMetric;
use App\Models\BenchmarkRun;
use App\Models\Dataset;
use App\Services\BenchmarkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class BenchmarkController extends Controller
{
    public function index(): View
    {
        $stats = $this->dashboardStats();
        $chartData = $this->buildDashboardCharts($stats);

        return view('benchmarks.index', compact('stats', 'chartData'));
    }

    public function history(): View
    {
        $runs = BenchmarkRun::with(['dataset', 'architecture', 'metric'])
            ->latest()
            ->paginate(15);

        return view('benchmarks.history', compact('runs'));
    }

    public function create(): View
    {
        return view('benchmarks.create', [
            'datasets' => Dataset::orderBy('name')->get(),
            'architectures' => Architecture::orderBy('name')->get(),
        ]);
    }

    public function store(StoreBenchmarkRequest $request, BenchmarkService $service): RedirectResponse
    {
        $run = BenchmarkRun::create([
            'id' => (string) Str::uuid(),
            'dataset_id' => $request->validated('dataset_id'),
            'architecture_id' => $request->validated('architecture_id'),
            'status' => 'pending',
        ]);

        try {
            $service->run($run);
        } catch (\Throwable $e) {
            return redirect()
                ->route('benchmarks.show', $run)
                ->with('error', 'Benchmark failed: '.$e->getMessage());
        }

        return redirect()
            ->route('benchmarks.show', $run)
            ->with('success', 'Benchmark completed successfully.');
    }

    public function show(BenchmarkRun $benchmark): View
    {
        $benchmark->load(['dataset', 'architecture', 'metric']);

        return view('benchmarks.show', ['run' => $benchmark]);
    }

    public function destroy(BenchmarkRun $benchmark): RedirectResponse
    {
        $benchmark->delete();

        return redirect()
            ->route('benchmarks.history')
            ->with('success', 'Benchmark run deleted.');
    }

    public function charts(Request $request): View
    {
        $datasets = Dataset::orderBy('name')->get();
        $architectures = Architecture::orderBy('name')->get();

        $query = BenchmarkRun::with(['dataset', 'architecture', 'metric'])
            ->where('status', 'completed')
            ->whereHas('metric');

        if ($request->filled('dataset_id')) {
            $query->where('dataset_id', $request->integer('dataset_id'));
        }

        if ($request->filled('architecture_id')) {
            $query->where('architecture_id', $request->integer('architecture_id'));
        }

        $runs = $query->latest()->get();

        $comparison = $this->buildComparison($runs);

        $chartData = [
            'labels' => $runs->map(fn ($run) => $run->architecture->name.' / '.$run->dataset->name)->values(),
            'f1' => $runs->pluck('metric.f1_score')->map(fn ($v) => $this->metricFloat($v))->values(),
            'latency' => $runs->pluck('metric.latency_ms')->map(fn ($v) => $this->metricFloat($v))->values(),
            'throughput' => $runs->pluck('metric.throughput_ops_per_sec')->map(fn ($v) => $this->metricFloat($v))->values(),
            'energy' => $runs->pluck('metric.energy_joules_per_op')->map(fn ($v) => $this->metricFloat($v))->values(),
            'fpr' => $runs->pluck('metric.false_positive_rate')->map(fn ($v) => $this->metricFloat($v))->values(),
        ];

        return view('benchmarks.charts', compact('datasets', 'architectures', 'runs', 'chartData', 'comparison'));
    }

    private function buildComparison($runs): array
    {
        $grouped = $runs->groupBy(fn ($run) => $run->dataset->name);

        $rows = [];
        foreach ($grouped as $datasetName => $datasetRuns) {
            $neuro = $datasetRuns->filter(fn ($r) => $r->architecture->is_neuromorphic);
            $baseline = $datasetRuns->filter(fn ($r) => ! $r->architecture->is_neuromorphic);

            if ($neuro->isEmpty() || $baseline->isEmpty()) {
                continue;
            }

            $avg = fn ($collection, $field) => $this->metricFloat($collection->avg(fn ($r) => $r->metric->{$field}));

            $rows[] = [
                'dataset' => $datasetName,
                'neuro_f1' => $avg($neuro, 'f1_score'),
                'baseline_f1' => $avg($baseline, 'f1_score'),
                'neuro_latency' => $this->metricFloat($neuro->avg(fn ($r) => $r->metric->latency_ms)),
                'baseline_latency' => $this->metricFloat($baseline->avg(fn ($r) => $r->metric->latency_ms)),
                'neuro_energy' => $this->metricFloat($neuro->avg(fn ($r) => $r->metric->energy_joules_per_op)),
                'baseline_energy' => $this->metricFloat($baseline->avg(fn ($r) => $r->metric->energy_joules_per_op)),
                'neuro_fpr' => $avg($neuro, 'false_positive_rate'),
                'baseline_fpr' => $avg($baseline, 'false_positive_rate'),
            ];
        }

        return $rows;
    }

    private function dashboardStats(): array
    {
        return [
            'total_runs' => BenchmarkRun::count(),
            'avg_f1' => BenchmarkMetric::avg('f1_score') ?? 0,
            'avg_latency' => BenchmarkMetric::avg('latency_ms') ?? 0,
            'avg_energy' => BenchmarkMetric::avg('energy_joules_per_op') ?? 0,
        ];
    }

    private function buildDashboardCharts(array $stats): array
    {
        $allRuns = BenchmarkRun::with('architecture')->get()->groupBy(fn ($run) => $run->architecture->name);

        $completedRuns = BenchmarkRun::with(['architecture', 'metric'])
            ->where('status', 'completed')
            ->whereHas('metric')
            ->get()
            ->groupBy(fn ($run) => $run->architecture->name);

        return [
            'kpi' => [
                'labels' => ['Avg F1-score', 'Avg Latency (ms)', 'Avg Energy (J/Op)'],
                'values' => [
                    $this->metricFloat($stats['avg_f1']),
                    $this->metricFloat($stats['avg_latency']),
                    $this->metricFloat($stats['avg_energy']),
                ],
            ],
            'total_runs' => [
                'labels' => $allRuns->keys()->values()->all(),
                'values' => $allRuns->map->count()->values()->all(),
            ],
            'f1_by_architecture' => [
                'labels' => $completedRuns->keys()->values()->all(),
                'values' => $completedRuns->map(fn ($group) => $this->metricFloat($group->avg(fn ($r) => $r->metric->f1_score)))->values()->all(),
            ],
            'latency_by_architecture' => [
                'labels' => $completedRuns->keys()->values()->all(),
                'values' => $completedRuns->map(fn ($group) => $this->metricFloat($group->avg(fn ($r) => $r->metric->latency_ms)))->values()->all(),
            ],
            'energy_by_architecture' => [
                'labels' => $completedRuns->keys()->values()->all(),
                'values' => $completedRuns->map(fn ($group) => $this->metricFloat($group->avg(fn ($r) => $r->metric->energy_joules_per_op)))->values()->all(),
            ],
        ];
    }

    private function metricFloat(mixed $value): float
    {
        return (float) $value;
    }
}
