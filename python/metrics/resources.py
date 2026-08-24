"""Process memory and CPU/GPU utilization during inference."""

from __future__ import annotations

import os
import time
from typing import Callable

from metrics.performance import profile_inference


def _memory_mb() -> float:
    try:
        import psutil

        return float(psutil.Process(os.getpid()).memory_info().rss / (1024 * 1024))
    except Exception:
        return 0.0


def _cpu_percent() -> float:
    try:
        import psutil

        psutil.cpu_percent(interval=None)
        time.sleep(0.05)
        return float(psutil.cpu_percent(interval=0.1))
    except Exception:
        return 0.0


def _gpu_percent() -> float:
    try:
        import torch

        if torch.cuda.is_available():
            util = getattr(torch.cuda, "utilization", None)
            if callable(util):
                return float(util())
            return 100.0
    except Exception:
        pass
    return 0.0


def profile_with_resources(predict_fn: Callable, X, energy_factor: float = 1e-9) -> dict:
    rss_before = _memory_mb()
    cpu_sample = _cpu_percent()
    perf = profile_inference(predict_fn, X, energy_factor=energy_factor)
    rss_after = _memory_mb()

    perf["memory_mb"] = float(max(rss_before, rss_after))
    perf["cpu_utilization"] = float(cpu_sample)
    perf["gpu_utilization"] = float(_gpu_percent())
    return perf
