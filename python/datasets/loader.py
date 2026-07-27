"""Dataset loading utilities."""

from __future__ import annotations

from datasets.cicids_loader import load as load_cicids
from datasets.unsw_nb15_loader import load as load_unsw_nb15


def load_dataset(slug: str, data_path: str | None = None):
    if slug == "cicids":
        return load_cicids(data_path)
    if slug == "unsw_nb15":
        return load_unsw_nb15(data_path)
    raise ValueError(f"Unknown dataset slug: {slug}")
