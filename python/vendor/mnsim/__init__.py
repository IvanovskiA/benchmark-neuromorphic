"""
MNSIM-style Processing-In-Memory (IMC) array model.

MNSIM 2.0 (Tsinghua, IEEE TCAD 2023) is a behaviour-level PIM simulator
for CNN/CIFAR workloads and is not a pip API. This module adapts the
published hierarchical PIM model (crossbar MAC + DAC/ADC) to our tabular
IDS MLP so Laravel can treat it as the fifth architecture.

Reference: https://github.com/thu-nics/MNSIM-2.0
"""

from __future__ import annotations

import json
from pathlib import Path

import numpy as np

# Typical MNSIM 2.0 analog PIM defaults (RRAM crossbar).
_DEFAULT_CONFIG = {
    "array_size": 128,
    "cell_precision_bits": 4,
    "dac_precision_bits": 8,
    "adc_precision_bits": 8,
    "read_latency_ns": 10.0,
    "cell_read_energy_j": 1.5e-12,
    "dac_energy_j": 8.0e-13,
    "adc_energy_j": 2.0e-12,
}


def load_config() -> dict:
    path = Path(__file__).with_name("SimConfig.json")
    if path.is_file():
        with path.open(encoding="utf-8") as handle:
            data = json.load(handle)
        merged = dict(_DEFAULT_CONFIG)
        merged.update(data)
        return merged
    return dict(_DEFAULT_CONFIG)


def estimate_pim_cost(layer_shapes: list[tuple[int, int]], n_samples: int) -> dict:
    """Estimate IMC latency/energy for a list of (in_features, out_features) layers."""
    cfg = load_config()
    array = max(int(cfg["array_size"]), 1)
    total_macs = 0
    tiles = 0
    for inn, out in layer_shapes:
        total_macs += max(inn, 1) * max(out, 1) * max(n_samples, 1)
        tiles += int(np.ceil(inn / array) * np.ceil(out / array))

    tiles = max(tiles, 1)
    latency_s = (tiles * float(cfg["read_latency_ns"]) * 1e-9)
    energy_j = total_macs * (
        float(cfg["cell_read_energy_j"])
        + float(cfg["dac_energy_j"])
        + float(cfg["adc_energy_j"])
    )
    ops = max(total_macs, 1)
    return {
        "latency_ms": float(latency_s * 1000.0 / max(n_samples, 1)),
        "throughput_ops_per_sec": float(n_samples / latency_s) if latency_s > 0 else 0.0,
        "energy_joules_per_op": float(energy_j / ops),
        "tiles": tiles,
        "macs": ops,
    }
