from __future__ import annotations

import os
import sys

import numpy as np

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, ROOT)

from backends.gpu_baseline import run  # noqa: E402


def test_require_cuda_raises_without_gpu() -> None:
    X = np.zeros((20, 4))
    y = np.array([0, 1] * 10)
    try:
        run(X, y, X, y, require_cuda=True)
    except RuntimeError as exc:
        print("require_cuda ok:", exc)
        return
    raise SystemExit("expected CUDA require to fail without GPU")


if __name__ == "__main__":
    test_require_cuda_raises_without_gpu()
