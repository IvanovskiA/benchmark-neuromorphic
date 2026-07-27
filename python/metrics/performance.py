"""Performance and energy metrics."""

import time


def profile_inference(predict_fn, X, energy_factor: float = 1e-9) -> dict:
    start = time.perf_counter()
    predictions = predict_fn(X)
    elapsed = time.perf_counter() - start

    n_samples = len(X)
    latency_ms = (elapsed / max(n_samples, 1)) * 1000
    throughput = n_samples / elapsed if elapsed > 0 else 0.0
    total_ops = n_samples * max(getattr(X, "shape", [n_samples, 1])[1], 1)
    energy_joules_per_op = (elapsed * energy_factor * total_ops) / max(total_ops, 1)

    return {
        "latency_ms": float(latency_ms),
        "throughput_ops_per_sec": float(throughput),
        "energy_joules_per_op": float(energy_joules_per_op),
        "predictions": predictions,
    }
