#!/usr/bin/env python3
"""Benchmark CLI entry point."""

from __future__ import annotations

import argparse
import os
import sys
import traceback

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from backends import cpu_baseline, gpu_baseline, ibm_nscs, lava_loihi, mnsim_imc
from datasets.loader import load_dataset
from db.connection import fetch_run
from db.writer import insert_metric, update_run_status

BACKENDS = {
    "cpu_baseline": cpu_baseline.run,
    "gpu_baseline": gpu_baseline.run,
    "lava_loihi": lava_loihi.run,
    "ibm_nscs": ibm_nscs.run,
    "mnsim_imc": mnsim_imc.run,
}


def main() -> int:
    parser = argparse.ArgumentParser(description="Run neuromorphic benchmark")
    parser.add_argument("--run-id", required=True, help="Benchmark run UUID")
    args = parser.parse_args()

    try:
        update_run_status(args.run_id, "running")
        run = fetch_run(args.run_id)

        dataset_slug = run["dataset_slug"]
        env_key = "CICIDS_PATH" if dataset_slug == "cicids" else "UNSW_NB15_PATH"
        data_path = os.getenv(env_key) or os.getenv("DATASET_PATH")
        X_train, X_test, y_train, y_test = load_dataset(dataset_slug, data_path)

        backend_fn = BACKENDS.get(run["architecture_slug"])
        if not backend_fn:
            raise ValueError(f"Unsupported architecture: {run['architecture_slug']}")

        metrics = backend_fn(X_train, y_train, X_test, y_test)
        insert_metric(args.run_id, metrics)
        update_run_status(args.run_id, "completed")
        print(f"Benchmark completed: {metrics}")
        return 0
    except Exception as exc:
        update_run_status(args.run_id, "failed", str(exc))
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
