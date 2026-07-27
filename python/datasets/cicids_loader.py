"""CICIDS dataset loader (2025-updated network traffic features)."""

from __future__ import annotations

import os

import numpy as np
import pandas as pd
from sklearn.model_selection import train_test_split


CICIDS_COLUMNS = [
    "Destination Port",
    "Flow Duration",
    "Total Fwd Packets",
    "Total Backward Packets",
    "Total Length of Fwd Packets",
    "Total Length of Bwd Packets",
    "Fwd Packet Length Max",
    "Bwd Packet Length Max",
    "Flow Bytes/s",
    "Flow Packets/s",
    "Label",
]


def _synthetic_cicids(n_samples: int = 3000, seed: int = 42):
    rng = np.random.default_rng(seed)
    n_features = len(CICIDS_COLUMNS) - 1
    X = rng.exponential(scale=1.0, size=(n_samples, n_features))
    attack_score = X[:, 0] * 0.3 + X[:, 2] * 0.4 + rng.normal(0, 0.2, n_samples)
    y = (attack_score > np.median(attack_score)).astype(int)
    return X, y


def resolve_path(explicit_path: str | None = None) -> str | None:
    candidates = [
        explicit_path,
        os.getenv("CICIDS_PATH"),
        "/data/cicids.csv",
        "/data/CICIDS.csv",
    ]
    for path in candidates:
        if path and os.path.isfile(path):
            return path
    return None


def load(data_path: str | None = None):
    path = resolve_path(data_path)
    if path:
        df = pd.read_csv(path)
        label_col = "Label" if "Label" in df.columns else df.columns[-1]
        y = (df[label_col].astype(str).str.lower() != "benign").astype(int).values
        X = df.drop(columns=[label_col]).select_dtypes(include=[np.number]).fillna(0).values
    else:
        X, y = _synthetic_cicids()

    return train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)
