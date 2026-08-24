"""Combine inference, classification, and resource metrics."""

from __future__ import annotations


def pack_metrics(perf: dict, accuracy: dict, backend: str) -> dict:
    payload = {k: v for k, v in perf.items() if k != "predictions"}
    payload.update(accuracy)
    payload["backend"] = backend
    return payload
