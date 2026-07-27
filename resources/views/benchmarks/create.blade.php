<x-layouts.app :heading="'New Benchmark'" :breadcrumb="'Configure and run'">
    <div class="mx-auto max-w-lg">
        <x-ui.card>
            <h2 class="mb-6 text-xl font-bold text-slate-900">Start new benchmark</h2>

            <form action="{{ route('benchmarks.store', [], false) }}" method="POST" class="space-y-6">
                @csrf

                <x-ui.select
                    label="Dataset"
                    name="dataset_id"
                    :options="$datasets->pluck('name', 'id')->toArray()"
                    :selected="old('dataset_id')"
                />

                <x-ui.select
                    label="Architecture"
                    name="architecture_id"
                    :options="$architectures->pluck('name', 'id')->toArray()"
                    :selected="old('architecture_id')"
                />

                <x-ui.button type="submit" class="w-full">Start benchmark</x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>
