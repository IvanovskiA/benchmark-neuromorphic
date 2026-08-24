<x-layouts.app :heading="'Charts'" :breadcrumb="'Metrics visualization'">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900">Performance Analysis & Results</h2>
        <p class="mt-1 text-sm text-slate-500">Graphs and tables comparing non-Von Neumann vs CPU/GPU baselines</p>
    </div>

    <x-ui.card class="mb-8">
        <form method="GET" action="{{ route('benchmarks.charts', [], false) }}" class="grid gap-4 md:grid-cols-3 md:items-end">
            <x-ui.select
                label="Dataset"
                name="dataset_id"
                :options="$datasets->pluck('name', 'id')->toArray()"
                :selected="request('dataset_id')"
            />
            <x-ui.select
                label="Architecture"
                name="architecture_id"
                :options="$architectures->pluck('name', 'id')->toArray()"
                :selected="request('architecture_id')"
            />
            <x-ui.button type="submit">Apply</x-ui.button>
        </form>
    </x-ui.card>

    @if($runs->isEmpty())
        <x-ui.card>
            <p class="text-center text-sm text-slate-500">No completed runs for the selected filters.</p>
        </x-ui.card>
    @else
        @if(count($comparison) > 0)
            <x-ui.card class="mb-8">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Comparison Table — Neuromorphic vs Baseline</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-slate-700">Dataset</th>
                                <th class="px-4 py-2 text-left font-semibold text-neuromorphic">Neuro F1</th>
                                <th class="px-4 py-2 text-left font-semibold text-baseline">Baseline F1</th>
                                <th class="px-4 py-2 text-left font-semibold text-neuromorphic">Neuro Latency (ms)</th>
                                <th class="px-4 py-2 text-left font-semibold text-baseline">Baseline Latency (ms)</th>
                                <th class="px-4 py-2 text-left font-semibold text-neuromorphic">Neuro Energy (J/Op)</th>
                                <th class="px-4 py-2 text-left font-semibold text-baseline">Baseline Energy (J/Op)</th>
                                <th class="px-4 py-2 text-left font-semibold text-neuromorphic">Neuro FPR</th>
                                <th class="px-4 py-2 text-left font-semibold text-baseline">Baseline FPR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($comparison as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-2 font-medium text-slate-900">{{ $row['dataset'] }}</td>
                                    <td class="px-4 py-2 font-mono text-neuromorphic">{{ \App\Support\MetricsFormat::table($row['neuro_f1']) }}</td>
                                    <td class="px-4 py-2 font-mono text-baseline">{{ \App\Support\MetricsFormat::table($row['baseline_f1']) }}</td>
                                    <td class="px-4 py-2 font-mono">{{ \App\Support\MetricsFormat::table($row['neuro_latency']) }}</td>
                                    <td class="px-4 py-2 font-mono">{{ \App\Support\MetricsFormat::table($row['baseline_latency']) }}</td>
                                    <td class="px-4 py-2 font-mono">{{ \App\Support\MetricsFormat::table($row['neuro_energy']) }}</td>
                                    <td class="px-4 py-2 font-mono">{{ \App\Support\MetricsFormat::table($row['baseline_energy']) }}</td>
                                    <td class="px-4 py-2 font-mono">{{ \App\Support\MetricsFormat::table($row['neuro_fpr']) }}</td>
                                    <td class="px-4 py-2 font-mono">{{ \App\Support\MetricsFormat::table($row['baseline_fpr']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif

        <x-ui.card class="mb-8">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">All results</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Dataset</th>
                            <th class="px-3 py-2 text-left font-semibold">Architecture</th>
                            <th class="px-3 py-2 text-left font-semibold">F1</th>
                            <th class="px-3 py-2 text-left font-semibold">Latency</th>
                            <th class="px-3 py-2 text-left font-semibold">Throughput</th>
                            <th class="px-3 py-2 text-left font-semibold">Energy</th>
                            <th class="px-3 py-2 text-left font-semibold">FPR</th>
                            <th class="px-3 py-2 text-left font-semibold">Accuracy</th>
                            <th class="px-3 py-2 text-left font-semibold">Precision</th>
                            <th class="px-3 py-2 text-left font-semibold">Recall</th>
                            <th class="px-3 py-2 text-left font-semibold">ROC-AUC</th>
                            <th class="px-3 py-2 text-left font-semibold">Memory</th>
                            <th class="px-3 py-2 text-left font-semibold">CPU %</th>
                            <th class="px-3 py-2 text-left font-semibold">GPU %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($runs as $run)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2">{{ $run->dataset->name }}</td>
                                <td class="px-3 py-2">{{ $run->architecture->name }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->f1_score) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->latency_ms) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->throughput_ops_per_sec) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->energy_joules_per_op) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->false_positive_rate) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->accuracy) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->precision_score) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->recall) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->roc_auc) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->memory_mb) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->cpu_utilization) }}</td>
                                <td class="px-3 py-2 font-mono">{{ \App\Support\MetricsFormat::table($run->metric->gpu_utilization) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-ui.card>
                <h3 class="mb-4 text-lg font-semibold text-slate-900">F1-score (Accuracy)</h3>
                <canvas id="f1Chart" height="260"></canvas>
            </x-ui.card>
            <x-ui.card>
                <h3 class="mb-4 text-lg font-semibold text-slate-900">False Positive Rate (FPR)</h3>
                <canvas id="fprChart" height="260"></canvas>
            </x-ui.card>
            <x-ui.card class="lg:col-span-2">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Primary Metrics — Latency / Throughput / Energy</h3>
                <canvas id="perfChart" height="260"></canvas>
            </x-ui.card>
            <x-ui.card>
                <h3 class="mb-4 text-lg font-semibold text-slate-900">Accuracy / Precision / Recall</h3>
                <canvas id="clsChart" height="260"></canvas>
            </x-ui.card>
            <x-ui.card>
                <h3 class="mb-4 text-lg font-semibold text-slate-900">ROC-AUC</h3>
                <canvas id="aucChart" height="260"></canvas>
            </x-ui.card>
            <x-ui.card class="lg:col-span-2">
                <h3 class="mb-4 text-lg font-semibold text-slate-900">ROC curves</h3>
                <canvas id="rocOverlayChart" height="260"></canvas>
            </x-ui.card>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const labels = @json($chartData['labels']);
                const f1 = @json($chartData['f1']);
                const fpr = @json($chartData['fpr']);
                const latency = @json($chartData['latency']);
                const throughput = @json($chartData['throughput']);
                const energy = @json($chartData['energy']);
                const accuracy = @json($chartData['accuracy']);
                const precision = @json($chartData['precision']);
                const recall = @json($chartData['recall']);
                const rocAuc = @json($chartData['roc_auc']);
                const rocSeries = @json($chartData['roc_series']);

                const formatTick = (v) => {
                    const n = Number(v);
                    if (n === 0) return '0';
                    if (Math.abs(n) < 0.001 || Math.abs(n) >= 1000) return n.toExponential(2);
                    return n.toPrecision(4);
                };

                const chartTooltip = {
                    callbacks: {
                        label: (ctx) => `${ctx.dataset.label}: ${formatTick(ctx.parsed.y)}`,
                    },
                };

                new Chart(document.getElementById('f1Chart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{ label: 'F1-score', data: f1, backgroundColor: '#8b5cf6' }],
                    },
                    options: {
                        responsive: true,
                        plugins: { tooltip: chartTooltip },
                        scales: { y: { beginAtZero: true, max: 1, ticks: { callback: formatTick } } },
                    },
                });

                new Chart(document.getElementById('fprChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{ label: 'FPR', data: fpr, backgroundColor: '#ef4444' }],
                    },
                    options: {
                        responsive: true,
                        plugins: { tooltip: chartTooltip },
                        scales: { y: { beginAtZero: true, max: 1, ticks: { callback: formatTick } } },
                    },
                });

                new Chart(document.getElementById('perfChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Latency (ms)', data: latency, backgroundColor: '#2563eb' },
                            { label: 'Throughput (ops/s)', data: throughput, backgroundColor: '#64748b' },
                            { label: 'Energy (J/Op)', data: energy, backgroundColor: '#10b981' },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: { tooltip: chartTooltip },
                        scales: {
                            y: {
                                type: 'logarithmic',
                                ticks: { callback: (v) => Number(v).toExponential(1) },
                            },
                        },
                    },
                });

                new Chart(document.getElementById('clsChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Accuracy', data: accuracy, backgroundColor: '#0ea5e9' },
                            { label: 'Precision', data: precision, backgroundColor: '#f59e0b' },
                            { label: 'Recall', data: recall, backgroundColor: '#ec4899' },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: { tooltip: chartTooltip },
                        scales: { y: { beginAtZero: true, max: 1, ticks: { callback: formatTick } } },
                    },
                });

                new Chart(document.getElementById('aucChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{ label: 'ROC-AUC', data: rocAuc, backgroundColor: '#8b5cf6' }],
                    },
                    options: {
                        responsive: true,
                        plugins: { tooltip: chartTooltip },
                        scales: { y: { beginAtZero: true, max: 1, ticks: { callback: formatTick } } },
                    },
                });

                const rocColors = ['#8b5cf6', '#2563eb', '#10b981', '#f59e0b', '#ef4444', '#64748b'];
                new Chart(document.getElementById('rocOverlayChart'), {
                    type: 'line',
                    data: {
                        datasets: (rocSeries || []).map((series, i) => ({
                            label: series.label,
                            data: (series.fpr || []).map((x, idx) => ({ x, y: (series.tpr || [])[idx] })),
                            borderColor: rocColors[i % rocColors.length],
                            backgroundColor: 'transparent',
                            pointRadius: 0,
                            tension: 0,
                        })),
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { type: 'linear', min: 0, max: 1, title: { display: true, text: 'FPR' } },
                            y: { min: 0, max: 1, title: { display: true, text: 'TPR' } },
                        },
                    },
                });
            });
        </script>
    @endif
</x-layouts.app>
