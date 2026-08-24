"""Unit checks for classification metrics (legacy F1/FPR plus extensions)."""

from __future__ import annotations

import os
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, ROOT)

from metrics.accuracy import compute_metrics  # noqa: E402


def test_compute_metrics_legacy_and_new() -> None:
    y_true = [0, 0, 1, 1]
    y_pred = [0, 1, 1, 1]
    y_score = [0.1, 0.6, 0.8, 0.9]
    m = compute_metrics(y_true, y_pred, y_score)
    assert "f1_score" in m and "false_positive_rate" in m
    assert 0.0 <= m["f1_score"] <= 1.0
    assert 0.0 <= m["false_positive_rate"] <= 1.0
    assert 0.0 <= m["accuracy"] <= 1.0
    assert 0.0 <= m["precision_score"] <= 1.0
    assert 0.0 <= m["recall"] <= 1.0
    assert 0.0 <= m["roc_auc"] <= 1.0
    assert "fpr" in m["roc_curve"] and "tpr" in m["roc_curve"]
    print("compute_metrics ok")


def test_backends_registered() -> None:
    cli = os.path.join(ROOT, "cli", "run_benchmark.py")
    text = open(cli, encoding="utf-8").read()
    assert '"ibm_nscs"' in text
    assert '"mnsim_imc"' in text
    assert '"cpu_baseline"' in text
    print("BACKENDS registry ok")


if __name__ == "__main__":
    test_compute_metrics_legacy_and_new()
    test_backends_registered()
