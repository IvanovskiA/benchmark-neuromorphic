"""Colab export keys match the Postgres writer; no Laravel required."""

from __future__ import annotations

import os
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, ROOT)

from colab.run_colab_cpu_gpu import METRIC_KEYS, serialize_metrics  # noqa: E402


WRITER_KEYS = {
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
}


def test_export_keys_cover_writer() -> None:
    missing = WRITER_KEYS - set(METRIC_KEYS)
    assert not missing, missing
    row = serialize_metrics(
        {
            "latency_ms": 0.1,
            "throughput_ops_per_sec": 10.0,
            "energy_joules_per_op": 1e-9,
            "f1_score": 0.8,
            "false_positive_rate": 0.1,
            "accuracy": 0.8,
            "precision_score": 0.8,
            "recall": 0.8,
            "roc_auc": 0.8,
            "memory_mb": 100.0,
            "cpu_utilization": 20.0,
            "gpu_utilization": 40.0,
            "roc_curve": {"fpr": [0, 1], "tpr": [0, 1]},
            "backend": "gpu_baseline",
        },
        "cicids",
        "gpu_baseline",
    )
    assert row["source"] == "colab_cuda"
    assert row["backend"] == "gpu_baseline"
    print("colab export keys ok")


if __name__ == "__main__":
    test_export_keys_cover_writer()
