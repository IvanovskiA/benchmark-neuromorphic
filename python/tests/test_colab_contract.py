"""Colab JSON keys must match the PostgreSQL writer."""

from __future__ import annotations

import os
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, ROOT)

from colab.run_colab_cpu_gpu import METRIC_KEYS  # noqa: E402


WRITER = os.path.join(ROOT, "db", "writer.py")
CLI = os.path.join(ROOT, "cli", "run_benchmark.py")


def test_metric_keys_match_writer() -> None:
    text = open(WRITER, encoding="utf-8").read()
    for key in METRIC_KEYS:
        if key == "backend":
            continue
        assert key in text, f"missing writer column/key: {key}"
    print("colab metric keys match writer")


def test_other_backends_untouched() -> None:
    text = open(CLI, encoding="utf-8").read()
    assert '"ibm_nscs"' in text
    assert '"mnsim_imc"' in text
    assert '"lava_loihi"' in text
    assert '"gpu_baseline"' in text
    assert '"cpu_baseline"' in text
    print("BACKENDS still include Lava, TrueNorth, IMC, CPU, GPU")


def test_assert_cuda_refuses_without_gpu() -> None:
    import torch
    from colab.run_colab_cpu_gpu import assert_cuda

    if torch.cuda.is_available():
        print("assert_cuda skipped (CUDA present)")
        return
    try:
        assert_cuda()
    except RuntimeError as exc:
        assert "CUDA" in str(exc)
        print("assert_cuda refuses without GPU")
        return
    raise AssertionError("assert_cuda should fail without CUDA")


if __name__ == "__main__":
    test_metric_keys_match_writer()
    test_other_backends_untouched()
    test_assert_cuda_refuses_without_gpu()
