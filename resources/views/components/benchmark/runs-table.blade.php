@props(['runs'])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">ID</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Dataset</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Architecture</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">F1</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Latency</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Date</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @foreach($runs as $run)
                <tr class="hover:bg-slate-50 {{ $run->architecture->is_neuromorphic ? 'border-l-4 border-violet-500' : '' }}">
                    <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($run->id, 8) }}</td>
                    <td class="px-4 py-3">{{ $run->dataset->name }}</td>
                    <td class="px-4 py-3">{{ $run->architecture->name }}</td>
                    <td class="px-4 py-3"><x-ui.badge :status="$run->status" /></td>
                    <td class="px-4 py-3 font-mono">{{ $run->metric?->f1_score !== null ? \App\Support\MetricsFormat::f1($run->metric->f1_score) : '—' }}</td>
                    <td class="px-4 py-3 font-mono">{{ $run->metric?->latency_ms !== null ? \App\Support\MetricsFormat::latency($run->metric->latency_ms) : '—' }}</td>
                    <td class="px-4 py-3">{{ $run->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('benchmarks.show', $run, false) }}" class="text-brand-600 hover:text-brand-700">Details</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if(method_exists($runs, 'links'))
    <div class="mt-4">{{ $runs->links() }}</div>
@endif
