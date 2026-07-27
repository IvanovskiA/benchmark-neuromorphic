"""Generate sample CICIDS / UNSW-NB15 CSV files for MVP demonstration."""

from __future__ import annotations

import os

import numpy as np
import pandas as pd

CICIDS_COLUMNS = [
    "Destination Port", "Flow Duration", "Total Fwd Packets", "Total Backward Packets",
    "Total Length of Fwd Packets", "Total Length of Bwd Packets", "Fwd Packet Length Max",
    "Bwd Packet Length Max", "Flow Bytes/s", "Flow Packets/s", "Label",
]

UNSW_COLUMNS = [
    "dur", "proto", "service", "state", "spkts", "dpkts",
    "sbytes", "dbytes", "rate", "sload", "dload", "label",
]


def generate(output_dir: str = "/data") -> None:
    os.makedirs(output_dir, exist_ok=True)
    rng = np.random.default_rng(42)

    n_c = len(CICIDS_COLUMNS) - 1
    X_c = rng.exponential(scale=1.0, size=(2000, n_c))
    attack_c = X_c[:, 0] * 0.3 + X_c[:, 2] * 0.4 + rng.normal(0, 0.2, 2000)
    y_c = (attack_c > np.median(attack_c)).astype(int)
    cicids_df = pd.DataFrame(X_c, columns=CICIDS_COLUMNS[:-1])
    cicids_df["Label"] = np.where(y_c == 1, "Attack", "Benign")
    cicids_path = os.path.join(output_dir, "cicids.csv")
    cicids_df.to_csv(cicids_path, index=False)

    n_u = len(UNSW_COLUMNS) - 1
    X_u = rng.gamma(shape=2.0, scale=1.0, size=(2000, n_u))
    attack_u = X_u[:, 4] + X_u[:, 5] * 0.5 + rng.normal(0, 0.3, 2000)
    y_u = (attack_u > np.percentile(attack_u, 55)).astype(int)
    unsw_df = pd.DataFrame(X_u, columns=UNSW_COLUMNS[:-1])
    unsw_df["label"] = np.where(y_u == 1, "attack", "normal")
    unsw_path = os.path.join(output_dir, "unsw_nb15.csv")
    unsw_df.to_csv(unsw_path, index=False)

    print(f"Generated: {cicids_path}, {unsw_path}")


if __name__ == "__main__":
    generate(os.getenv("DATASET_OUTPUT", "/data"))
