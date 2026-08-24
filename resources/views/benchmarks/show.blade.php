<x-layouts.app :heading="'Run Details'" :breadcrumb="$run->id">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Benchmark Details</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $run->dataset->name }} · {{ $run->architecture->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('benchmarks.index', [], false) }}"><x-ui.button variant="secondary">Back</x-ui.button></a>
            <form action="{{ route('benchmarks.destroy', $run, false) }}" method="POST" onsubmit="return confirm('Delete this run?')">
                @csrf
                @method('DELETE')
                <x-ui.button variant="danger" type="submit">Delete</x-ui.button>
            </form>
        </div>
    </div>

    <div class="mb-6">
        <x-ui.badge :status="$run->status" />
    </div>

    @if($run->error_message)
        <x-ui.alert type="error" :message="$run->error_message" class="mb-6" />
    @endif

    @if($run->metric)
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-5">
            <x-ui.stat-card label="F1-score" :value="\App\Support\MetricsFormat::card($run->metric->f1_score)" />
            <x-ui.stat-card label="FPR" :value="\App\Support\MetricsFormat::card($run->metric->false_positive_rate)" />
            <x-ui.stat-card label="Latency (ms)" :value="\App\Support\MetricsFormat::card($run->metric->latency_ms)" />
            <x-ui.stat-card label="Throughput (ops/s)" :value="\App\Support\MetricsFormat::card($run->metric->throughput_ops_per_sec)" />
            <x-ui.stat-card label="Energy (J/Op)" :value="\App\Support\MetricsFormat::card($run->metric->energy_joules_per_op)" />
            <x-ui.stat-card label="Accuracy" :value="\App\Support\MetricsFormat::card($run->metric->accuracy)" />
            <x-ui.stat-card label="Precision" :value="\App\Support\MetricsFormat::card($run->metric->precision_score)" />
            <x-ui.stat-card label="Recall" :value="\App\Support\MetricsFormat::card($run->metric->recall)" />
            <x-ui.stat-card label="ROC-AUC" :value="\App\Support\MetricsFormat::card($run->metric->roc_auc)" />
            <x-ui.stat-card label="Memory (MB)" :value="\App\Support\MetricsFormat::card($run->metric->memory_mb)" />
            <x-ui.stat-card label="CPU Utilization (%)" :value="\App\Support\MetricsFormat::card($run->metric->cpu_utilization)" />
            <x-ui.stat-card label="GPU Utilization (%)" :value="\App\Support\MetricsFormat::card($run->metric->gpu_utilization)" />
        </div>

        @php
            $roc = $run->metric->roc_curve ?? [];
            $rocFpr = $roc['fpr'] ?? [];
            $rocTpr = $roc['tpr'] ?? [];
        @endphp
        @if(count($rocFpr) > 1)
            <div style="height: 2.5rem;" aria-hidden="true"></div>
            <x-ui.card>
                <h3 class="mb-4 text-lg font-semibold text-slate-900">ROC curve</h3>
                <canvas id="rocChart" height="220"></canvas>
            </x-ui.card>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const fpr = @json($rocFpr);
                    const tpr = @json($rocTpr);
                    new Chart(document.getElementById('rocChart'), {
                        type: 'line',
                        data: {
                            labels: fpr,
                            datasets: [{
                                label: 'TPR',
                                data: tpr.map((y, i) => ({ x: fpr[i], y })),
                                borderColor: '#8b5cf6',
                                backgroundColor: 'transparent',
                                pointRadius: 0,
                                tension: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { type: 'linear', min: 0, max: 1, title: { display: true, text: 'False Positive Rate' } },
                                y: { min: 0, max: 1, title: { display: true, text: 'True Positive Rate' } },
                            },
                        },
                    });
                });
            </script>
        @endif
    @else
        <x-ui.card>
            <p class="text-sm text-slate-500">No metrics available for this run.</p>
        </x-ui.card>
    @endif
</x-layouts.app>
