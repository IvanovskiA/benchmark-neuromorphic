<x-layouts.app :heading="'Methodology'" :breadcrumb="'Experimental setup & framework'">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-slate-900">Methodology & Experimental Setup</h2>
        <p class="mt-1 text-sm text-slate-500">
            Benchmarking emerging non-Von Neumann architectures for real-time cybersecurity threat detection
        </p>
    </div>

    <div class="space-y-6">
        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900">Architecture Selection</h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                Without access to physical neuromorphic silicon, the evaluation pipeline deploys SNN threat
                detectors onto established architectural simulation environments:
            </p>
            <ul class="mt-4 list-inside list-disc space-y-2 text-sm text-slate-600">
                <li><span class="font-medium text-neuromorphic">Intel Lava</span> — Loihi CPU simulation via <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">Loihi1SimCfg</code></li>
                <li><span class="font-medium text-neuromorphic">IBM NSCS</span> — TrueNorth neuromorphic simulator (LIF neuron model, binary synapses)</li>
                <li><span class="font-medium text-slate-800">MNSIM IMC</span> — In-Memory Computing / PIM adapter of MNSIM 2.0 (crossbar MAC + DAC/ADC), not a spiking chip</li>
                <li><span class="font-medium text-baseline">CPU / GPU baselines</span> — Von Neumann MLP (sklearn) and PyTorch feed-forward network (64–32–1) for direct comparison</li>
            </ul>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">
                The local Docker host has no NVIDIA GPU, so <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">gpu_baseline</code>
                uses a CPU fallback (<code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">gpu_baseline_cpu_fallback</code>).
                GPU utilization = 0% in that environment is expected, not a measurement error.
                True GPU numbers come from repeating the same Python backends on
                <strong>Google Colab with NVIDIA CUDA</strong> (same CICIDS / UNSW-NB15 splits and the same PyTorch model).
                For a fair conventional comparison, Colab runs both CPU baseline and GPU baseline on the same machine
                and the same data; those rows are imported into the Charts results table.
                Laravel, PostgreSQL, and Docker stay on the local host — only the Python benchmark runs in Colab.
            </p>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900">Threat Models & Datasets</h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                Real-world network traffic datasets simulate real-time attack detection scenarios:
            </p>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="font-medium text-slate-900">CICIDS</p>
                    <p class="mt-1 text-xs text-slate-500">Canadian Institute for Cybersecurity IDS dataset (2025-updated features)</p>
                    <p class="mt-2 font-mono text-xs text-slate-600">storage/datasets/cicids.csv</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="font-medium text-slate-900">UNSW-NB15</p>
                    <p class="mt-1 text-xs text-slate-500">University of NSW network-based intrusion detection dataset</p>
                    <p class="mt-2 font-mono text-xs text-slate-600">storage/datasets/unsw_nb15.csv</p>
                </div>
            </div>
            <p class="mt-4 text-sm text-slate-600">
                Traffic features are fed into <strong>Spiking Neural Network (SNN)</strong> threat detectors trained
                for binary classification (benign vs attack).
            </p>
        </x-ui.card>

        <x-ui.card>
            <h3 class="text-lg font-semibold text-slate-900">Benchmarking Framework</h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">
                Modular pipeline: Laravel dashboard → Python CLI → architecture backend → PostgreSQL metrics store.
            </p>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Metric category</th>
                            <th class="px-4 py-2 text-left font-semibold text-slate-700">Metrics</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="px-4 py-2 text-slate-600">Primary (hardware performance)</td>
                            <td class="px-4 py-2 font-mono text-slate-800">Latency, Throughput, Energy (J/Op), Memory, CPU/GPU Utilization</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-slate-600">Accuracy (classification)</td>
                            <td class="px-4 py-2 font-mono text-slate-800">FPR, F1-score, Accuracy, Precision, Recall, ROC-AUC</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>
