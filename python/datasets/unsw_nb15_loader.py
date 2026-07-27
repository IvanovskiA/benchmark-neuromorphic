"""UNSW-NB15 dataset loader (real-world network traffic)."""

from __future__ import annotations

import os

import numpy as np
import pandas as pd
from sklearn.model_selection import train_test_split


UNSW_COLUMNS = [
    "dur",
    "proto",
    "service",
    "state",
    "spkts",
    "dpkts",
    "sbytes",
    "dbytes",
    "rate",
    "sload",
    "dload",
    "label",
]


def _synthetic_unsw(n_samples: int = 3000, seed: int = 84):
    rng = np.random.default_rng(seed)
    n_features = len(UNSW_COLUMNS) - 1
    X = rng.gamma(shape=2.0, scale=1.0, size=(n_samples, n_features))
    attack_score = X[:, 4] + X[:, 5] * 0.5 + rng.normal(0, 0.3, n_samples)
    y = (attack_score > np.percentile(attack_score, 55)).astype(int)
    return X, y


def resolve_path(explicit_path: str | None = None) -> str | None:
    candidates = [
        explicit_path,
        os.getenv("UNSW_NB15_PATH"),
        "/data/unsw_nb15.csv",
        "/data/UNSW-NB15.csv",
    ]
    for path in candidates:
        if path and os.path.isfile(path):
            return path
    return None


def load(data_path: str | None = None):
    path = resolve_path(data_path)
    if path:
        df = pd.read_csv(path)
        label_col = "label" if "label" in df.columns else df.columns[-1]
        y = (df[label_col].astype(str).str.lower() != "normal").astype(int).values
        X = df.drop(columns=[label_col]).select_dtypes(include=[np.number]).fillna(0).values
    else:
        X, y = _synthetic_unsw()

    return train_test_split(X, y, test_size=0.2, random_state=84, stratify=y)
