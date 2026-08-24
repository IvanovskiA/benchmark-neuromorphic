"""Map IDS MLP weights onto the MNSIM PIM array cost model."""

from __future__ import annotations

from vendor.mnsim import estimate_pim_cost


def mlp_layer_shapes(n_features: int, hidden=(64, 32), n_outputs: int = 2) -> list[tuple[int, int]]:
    shapes = []
    prev = n_features
    for size in hidden:
        shapes.append((prev, size))
        prev = size
    shapes.append((prev, n_outputs))
    return shapes


def mnsim_hardware_metrics(n_features: int, n_samples: int) -> dict:
    shapes = mlp_layer_shapes(n_features)
    return estimate_pim_cost(shapes, n_samples)
