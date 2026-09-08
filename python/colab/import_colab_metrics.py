#!/usr/bin/env python3
"""Import Colab JSON metrics into local PostgreSQL (run inside Docker app)."""

from __future__ import annotations

import argparse
import json
import os
import sys
import uuid
from datetime import datetime, timezone

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
if ROOT not in sys.path:
    sys.path.insert(0, ROOT)

from db.connection import get_connection  # noqa: E402
from db.writer import insert_metric  # noqa: E402


def _load_results(path: str) -> list[dict]:
    with open(path, encoding="utf-8") as fh:
        payload = json.load(fh)
    if isinstance(payload, list):
        return payload
    results = payload.get("results")
    if not isinstance(results, list):
        raise ValueError("JSON must be a list or an object with a 'results' array")
    return results


def _lookup_ids(cur, dataset_slug: str, architecture_slug: str) -> tuple[int, int]:
    cur.execute(
        "SELECT id FROM datasets WHERE slug = %s AND deleted_at IS NULL",
        (dataset_slug,),
    )
    dataset = cur.fetchone()
    cur.execute(
        "SELECT id FROM architectures WHERE slug = %s AND deleted_at IS NULL",
        (architecture_slug,),
    )
    architecture = cur.fetchone()
    if not dataset:
        raise ValueError(f"Unknown dataset slug: {dataset_slug}")
    if not architecture:
        raise ValueError(f"Unknown architecture slug: {architecture_slug}")
    return int(dataset[0]), int(architecture[0])


def soft_delete_local_gpu_runs() -> int:
    with get_connection() as conn:
        with conn.cursor() as cur:
            cur.execute(
                """
                UPDATE benchmark_runs
                SET deleted_at = NOW(), updated_at = NOW()
                WHERE deleted_at IS NULL
                  AND architecture_id = (
                      SELECT id FROM architectures WHERE slug = 'gpu_baseline' AND deleted_at IS NULL
                  )
                """
            )
            updated = cur.rowcount
        conn.commit()
    return updated


def import_row(row: dict) -> str:
    dataset_slug = row.get("dataset")
    architecture_slug = row.get("architecture")
    if dataset_slug not in {"cicids", "unsw_nb15"}:
        raise ValueError(f"Unsupported dataset: {dataset_slug}")
    if architecture_slug not in {"cpu_baseline", "gpu_baseline"}:
        raise ValueError(f"Unsupported architecture: {architecture_slug}")

    run_id = str(uuid.uuid4())
    now = datetime.now(timezone.utc)

    with get_connection() as conn:
        with conn.cursor() as cur:
            dataset_id, architecture_id = _lookup_ids(cur, dataset_slug, architecture_slug)
            cur.execute(
                """
                INSERT INTO benchmark_runs (
                    id, dataset_id, architecture_id, status,
                    started_at, finished_at, error_message, created_at, updated_at
                ) VALUES (
                    %s, %s, %s, 'completed',
                    %s, %s, NULL, %s, %s
                )
                """,
                (run_id, dataset_id, architecture_id, now, now, now, now),
            )
        conn.commit()

    insert_metric(run_id, row)
    return run_id


def main() -> int:
    parser = argparse.ArgumentParser(description="Import Colab CPU/GPU JSON into benchmark DB")
    parser.add_argument("--json", required=True, help="Path to colab_results.json")
    parser.add_argument(
        "--soft-delete-local-gpu",
        action="store_true",
        default=True,
        help="Soft-delete existing local gpu_baseline runs before import (default)",
    )
    parser.add_argument(
        "--keep-local-gpu",
        action="store_true",
        help="Do not soft-delete existing gpu_baseline runs",
    )
    args = parser.parse_args()

    if args.keep_local_gpu:
        deleted = 0
    else:
        deleted = soft_delete_local_gpu_runs()
        print(f"Soft-deleted local gpu_baseline runs: {deleted}")

    rows = _load_results(os.path.abspath(args.json))
    imported = []
    for row in rows:
        run_id = import_row(row)
        imported.append((run_id, row.get("dataset"), row.get("architecture")))
        print(f"Imported {row.get('dataset')} / {row.get('architecture')} -> {run_id}")

    print(f"Done. {len(imported)} run(s) imported.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
