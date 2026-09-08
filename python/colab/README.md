# Colab CPU vs GPU baseline

Local Docker has no NVIDIA GPU. `gpu_baseline` falls back to CPU there, so GPU utilization = 0% is expected.

This folder repeats the **same** Python backends (`cpu_baseline`, `gpu_baseline`) and the **same** CICIDS / UNSW-NB15 loaders on Google Colab with CUDA. Laravel and PostgreSQL stay on the local machine.

## 1. Colab (produce JSON)

1. Open [Google Colab](https://colab.research.google.com/).
2. Runtime → Change runtime type → **GPU** (T4 / L4).
3. Upload this folder’s parent `python/` tree (or the whole repo) and optional CSVs (`cicids.csv`, `unsw_nb15.csv`).
4. Open `colab_cpu_gpu_benchmark.ipynb` and run all cells, or:

```bash
cd python
python colab/run_colab_cpu_gpu.py --datasets cicids,unsw_nb15 --output colab/colab_results.json
```

If no CSV is uploaded, the existing synthetic CICIDS / UNSW generators are used (same as Docker when files are missing).

Download `colab_results.json`.

## 2. Local Docker (import into Charts)

```bash
docker compose exec app python3 /var/www/html/python/colab/import_colab_metrics.py --json /var/www/html/python/colab/colab_results.json
```

By default this **soft-deletes** existing local `gpu_baseline` runs so Charts averages are not mixed with the CPU fallback. Neuromorphic / MNSIM rows are not touched.

To keep old GPU fallback rows:

```bash
docker compose exec app python3 /var/www/html/python/colab/import_colab_metrics.py --json /var/www/html/python/colab/colab_results.json --keep-local-gpu
```

Then open http://localhost:8080/benchmarks/charts (All results) and History.
