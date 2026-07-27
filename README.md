# Benchmark Neuromorphic MVP

MVP platform per *Објаснување на темата*: benchmarking emerging **non-Von Neumann** architectures for **real-time cybersecurity threat detection**.

## Quick Start

```bash
docker-compose up --build
```

Open: http://localhost:8080

## Scope (docx requirements)

| Component | Implementation |
|-----------|----------------|
| **Datasets** | CICIDS + UNSW-NB15 (`storage/datasets/*.csv`) |
| **SNN threat detectors** | snnTorch Spiking Neural Networks |
| **Intel Lava / Loihi** | `Loihi1SimCfg` CPU simulation (Lava framework) |
| **IBM NSCS / TrueNorth** | TrueNorth LIF neuron model (NSCS-style) |
| **Baselines** | CPU (sklearn MLP) + GPU (PyTorch, CPU fallback) |
| **Metrics** | Latency, Throughput, Energy (J/Op), FPR, F1-score |
| **Visualization** | Chart.js graphs + comparison table |
| **Methodology** | `/methodology` — architecture, datasets, framework |

## Stack

- Laravel 11 (Blade + Tailwind + Chart.js)
- PostgreSQL 16
- Python 3.10 benchmark engine (torch, snnTorch, lava-nc)
- Docker Compose

## Custom datasets

Replace sample CSVs in `storage/datasets/` or set env vars:

```env
CICIDS_PATH=/data/cicids.csv
UNSW_NB15_PATH=/data/unsw_nb15.csv
```

## Run benchmark manually

```bash
docker-compose exec python python /app/python/cli/run_benchmark.py --run-id=<UUID>
```
