#!/usr/bin/env python3
"""Run CPU then GPU baselines in Colab (or any CUDA host) on the same splits.

Does not talk to Laravel or PostgreSQL. Writes a JSON file for later import.
"""

from __future__ import annotations

import argparse
import json
import os
import sys
from datetime import datetime, timezone

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
if ROOT in sys.path:
    sys.path.remove(ROOT)
sys.path.insert(0, ROOT)

# Colab preinstalls HuggingFace `datasets`, which shadows python/datasets.
for name in list(sys.modules):
    if name == "datasets" or name.startswith("datasets."):
        del sys.modules[name]

from backends import cpu_baseline, gpu_baseline  # noqa: E402
from datasets.loader import load_dataset  # noqa: E402

DATASETS = ("cicids", "unsw_nb15")
METRIC_KEYS = (
    "latency_ms",
    "throughput_ops_per_sec",
    "energy_joules_per_op",
    "f1_score",
    "false_positive_rate",
    "accuracy",
    "precision_score",
    "recall",
    "roc_auc",
    "memory_mb",
    "cpu_utilization",
    "gpu_utilization",
    "roc_curve",
    "backend",
)


def _json_safe(value):
    if hasattr(value, "item"):
        return value.item()
    if isinstance(value, dict):
        return {str(k): _json_safe(v) for k, v in value.items()}
    if isinstance(value, (list, tuple)):
        return [_json_safe(v) for v in value]
    return value


def assert_cuda() -> str:
    import torch

    if not torch.cuda.is_available():
        raise RuntimeError(
            "CUDA is not available. In Colab: Runtime → Change runtime type → GPU. "
            "Refusing to record a fake GPU baseline."
        )
    return str(torch.cuda.get_device_name(0))


def run_pair(dataset_slug: str, data_path: str | None = None) -> list[dict]:
    X_train, X_test, y_train, y_test = load_dataset(dataset_slug, data_path)
    shape = {
        "n_train": int(len(X_train)),
        "n_test": int(len(X_test)),
        "n_features": int(X_train.shape[1]),
    }

    rows = []
    cpu_metrics = cpu_baseline.run(X_train, y_train, X_test, y_test)
    rows.append(_pack_row(dataset_slug, "cpu_baseline", cpu_metrics, shape, cuda_device=None))

    device_name = assert_cuda()
    gpu_metrics = gpu_baseline.run(X_train, y_train, X_test, y_test)
    if gpu_metrics.get("backend") == "gpu_baseline_cpu_fallback":
        raise RuntimeError("GPU backend fell back to CPU. CUDA must stay available for the GPU run.")
    rows.append(_pack_row(dataset_slug, "gpu_baseline", gpu_metrics, shape, cuda_device=device_name))
    return rows


def _pack_row(
    dataset_slug: str,
    architecture_slug: str,
    metrics: dict,
    shape: dict,
    cuda_device: str | None,
) -> dict:
    row = {
        "dataset": dataset_slug,
        "architecture": architecture_slug,
        "source": "google_colab",
        "split": shape,
        "cuda_device": cuda_device,
        "recorded_at": datetime.now(timezone.utc).isoformat(),
    }
    for key in METRIC_KEYS:
        if key in metrics:
            row[key] = _json_safe(metrics[key])
    return row


def main() -> int:
    parser = argparse.ArgumentParser(description="Colab CPU vs GPU benchmark (no Laravel)")
    parser.add_argument(
        "--datasets",
        default="cicids,unsw_nb15",
        help="Comma-separated slugs: cicids,unsw_nb15",
    )
    parser.add_argument("--cicids-path", default=os.getenv("CICIDS_PATH"))
    parser.add_argument("--unsw-path", default=os.getenv("UNSW_NB15_PATH"))
    parser.add_argument(
        "--output",
        default=os.path.join(os.path.dirname(os.path.abspath(__file__)), "colab_results.json"),
    )
    args = parser.parse_args()

    device_name = assert_cuda()
    print(f"CUDA device: {device_name}")

    slugs = [s.strip() for s in args.datasets.split(",") if s.strip()]
    results: list[dict] = []
    for slug in slugs:
        if slug not in DATASETS:
            raise ValueError(f"Unsupported dataset: {slug}")
        path = args.cicids_path if slug == "cicids" else args.unsw_path
        print(f"Running CPU then GPU on {slug} (same split)...")
        results.extend(run_pair(slug, path))

    os.makedirs(os.path.dirname(os.path.abspath(args.output)) or ".", exist_ok=True)
    with open(args.output, "w", encoding="utf-8") as fh:
        json.dump({"results": results}, fh, indent=2)
    print(f"Wrote {args.output} ({len(results)} runs)")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
