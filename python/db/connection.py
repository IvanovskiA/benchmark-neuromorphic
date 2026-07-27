"""Database connection helpers for Python benchmark engine."""

import os

import psycopg2
from psycopg2.extras import RealDictCursor


def get_connection():
    return psycopg2.connect(
        host=os.getenv("DB_HOST", "postgres"),
        port=os.getenv("DB_PORT", "5432"),
        dbname=os.getenv("DB_DATABASE", "benchmark"),
        user=os.getenv("DB_USERNAME", "benchmark"),
        password=os.getenv("DB_PASSWORD", "secret"),
    )


def fetch_run(run_id: str) -> dict:
    with get_connection() as conn:
        with conn.cursor(cursor_factory=RealDictCursor) as cur:
            cur.execute(
                """
                SELECT br.id, br.status, d.slug AS dataset_slug, a.slug AS architecture_slug
                FROM benchmark_runs br
                JOIN datasets d ON d.id = br.dataset_id
                JOIN architectures a ON a.id = br.architecture_id
                WHERE br.id = %s
                """,
                (run_id,),
            )
            row = cur.fetchone()
            if not row:
                raise ValueError(f"Benchmark run not found: {run_id}")
            return dict(row)
