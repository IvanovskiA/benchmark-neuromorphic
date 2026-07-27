<x-layouts.app :heading="'History'" :breadcrumb="'Benchmark runs'">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Benchmark History</h2>
            <p class="mt-1 text-sm text-slate-500">All benchmark runs and their metrics</p>
        </div>
        <a href="{{ route('benchmarks.create', [], false) }}">
            <x-ui.button>Start new benchmark</x-ui.button>
        </a>
    </div>

    <x-ui.card>
        @if($runs->isEmpty())
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 py-12 text-center">
                <p class="text-sm text-slate-500">No benchmark runs. Start a new benchmark.</p>
            </div>
        @else
            <x-benchmark.runs-table :runs="$runs" />
        @endif
    </x-ui.card>
</x-layouts.app>
