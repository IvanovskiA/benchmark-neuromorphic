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
        </div>
    @else
        <x-ui.card>
            <p class="text-sm text-slate-500">No metrics available for this run.</p>
        </x-ui.card>
    @endif
</x-layouts.app>
