<x-layouts.app :heading="'Dashboard'" :breadcrumb="'Benchmark overview'">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Benchmark Dashboard</h2>
            <p class="mt-1 text-sm text-slate-500">Non-Von Neumann architecture performance monitoring</p>
        </div>
        <a href="{{ route('benchmarks.create', [], false) }}">
            <x-ui.button>Start new benchmark</x-ui.button>
        </a>
    </div>

    <div class="mb-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Total Runs" :value="$stats['total_runs']" />
        <x-ui.stat-card label="Avg F1-score" :value="\App\Support\MetricsFormat::card($stats['avg_f1'])" />
        <x-ui.stat-card label="Avg Latency (ms)" :value="\App\Support\MetricsFormat::card($stats['avg_latency'])" />
        <x-ui.stat-card label="Avg Energy (J/Op)" :value="\App\Support\MetricsFormat::card($stats['avg_energy'])" />
    </div>

    @if($stats['total_runs'] === 0)
        <x-ui.card>
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                <p class="text-sm text-slate-500">No benchmark runs. Start a new benchmark to view charts.</p>
            </div>
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.card class="min-w-0">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">Total Runs by Architecture</h3>
                <canvas id="totalRunsChart" height="200"></canvas>
            </x-ui.card>
            <x-ui.card class="min-w-0">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">Avg F1-score by Architecture</h3>
                <canvas id="f1Chart" height="200"></canvas>
            </x-ui.card>
            <x-ui.card class="min-w-0">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">Avg Latency (ms) by Architecture</h3>
                <canvas id="latencyChart" height="200"></canvas>
            </x-ui.card>
            <x-ui.card class="min-w-0">
                <h3 class="mb-3 text-sm font-semibold text-slate-900">Avg Energy (J/Op) by Architecture</h3>
                <canvas id="energyChart" height="200"></canvas>
            </x-ui.card>
        </div>

        <x-ui.card class="mt-6">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">KPI Overview</h3>
            <canvas id="kpiChart" height="200"></canvas>
        </x-ui.card>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const chartData = @json($chartData);
                const archColors = ['#8b5cf6', '#2563eb', '#64748b', '#10b981', '#ef4444'];

                const formatTick = (v) => {
                    const n = Number(v);
                    if (n === 0) return '0';
                    if (Math.abs(n) < 0.001 || Math.abs(n) >= 1000) return n.toExponential(2);
                    return n.toPrecision(4);
                };

                const barOptions = (yScale = {}) => ({
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label ?? ''}: ${formatTick(ctx.parsed.y)}`,
                            },
                        },
                    },
                    scales: {
                        x: { ticks: { maxRotation: 45, minRotation: 0, font: { size: 10 } } },
                        y: { beginAtZero: true, ticks: { callback: formatTick }, ...yScale },
                    },
                });

                const logBarOptions = () => barOptions({
                    type: 'logarithmic',
                    min: undefined,
                    ticks: { callback: (v) => Number(v).toExponential(1) },
                });

                new Chart(document.getElementById('totalRunsChart'), {
                    type: 'doughnut',
                    data: {
                        labels: chartData.total_runs.labels,
                        datasets: [{
                            data: chartData.total_runs.values,
                            backgroundColor: archColors,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } },
                    },
                });

                new Chart(document.getElementById('f1Chart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.f1_by_architecture.labels,
                        datasets: [{
                            label: 'F1-score',
                            data: chartData.f1_by_architecture.values,
                            backgroundColor: '#8b5cf6',
                        }],
                    },
                    options: barOptions({ max: 1 }),
                });

                new Chart(document.getElementById('latencyChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.latency_by_architecture.labels,
                        datasets: [{
                            label: 'Latency (ms)',
                            data: chartData.latency_by_architecture.values,
                            backgroundColor: '#2563eb',
                        }],
                    },
                    options: logBarOptions(),
                });

                new Chart(document.getElementById('energyChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.energy_by_architecture.labels,
                        datasets: [{
                            label: 'Energy (J/Op)',
                            data: chartData.energy_by_architecture.values,
                            backgroundColor: '#10b981',
                        }],
                    },
                    options: logBarOptions(),
                });

                new Chart(document.getElementById('kpiChart'), {
                    type: 'bar',
                    data: {
                        labels: chartData.kpi.labels,
                        datasets: [{
                            label: 'Average',
                            data: chartData.kpi.values,
                            backgroundColor: ['#8b5cf6', '#2563eb', '#10b981'],
                        }],
                    },
                    options: logBarOptions(),
                });
            });
        </script>
    @endif
</x-layouts.app>
