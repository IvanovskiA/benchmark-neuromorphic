"""Write benchmark results to PostgreSQL."""

from db.connection import get_connection
import json


def update_run_status(run_id: str, status: str, error_message: str | None = None) -> None:
    with get_connection() as conn:
        with conn.cursor() as cur:
            if status == "running":
                cur.execute(
                    """
                    UPDATE benchmark_runs
                    SET status = %s, started_at = NOW(), error_message = NULL, updated_at = NOW()
                    WHERE id = %s
                    """,
                    (status, run_id),
                )
            elif status == "completed":
                cur.execute(
                    """
                    UPDATE benchmark_runs
                    SET status = %s, finished_at = NOW(), updated_at = NOW()
                    WHERE id = %s
                    """,
                    (status, run_id),
                )
            elif status == "failed":
                cur.execute(
                    """
                    UPDATE benchmark_runs
                    SET status = %s, finished_at = NOW(), error_message = %s, updated_at = NOW()
                    WHERE id = %s
                    """,
                    (status, error_message, run_id),
                )
            else:
                cur.execute(
                    "UPDATE benchmark_runs SET status = %s, updated_at = NOW() WHERE id = %s",
                    (status, run_id),
                )
        conn.commit()


def insert_metric(run_id: str, metrics: dict) -> None:
    roc_curve = metrics.get("roc_curve") or {"fpr": [0.0, 1.0], "tpr": [0.0, 1.0]}
    roc_json = json.dumps(roc_curve)

    with get_connection() as conn:
        with conn.cursor() as cur:
            cur.execute(
                """
                INSERT INTO benchmark_metrics (
                    benchmark_run_id, latency_ms, throughput_ops_per_sec,
                    energy_joules_per_op, f1_score, false_positive_rate,
                    accuracy, precision_score, recall, roc_auc,
                    memory_mb, cpu_utilization, gpu_utilization, roc_curve,
                    created_at, updated_at
                ) VALUES (
                    %s, %s, %s, %s, %s, %s,
                    %s, %s, %s, %s,
                    %s, %s, %s, %s::jsonb,
                    NOW(), NOW()
                )
                ON CONFLICT (benchmark_run_id) DO UPDATE SET
                    latency_ms = EXCLUDED.latency_ms,
                    throughput_ops_per_sec = EXCLUDED.throughput_ops_per_sec,
                    energy_joules_per_op = EXCLUDED.energy_joules_per_op,
                    f1_score = EXCLUDED.f1_score,
                    false_positive_rate = EXCLUDED.false_positive_rate,
                    accuracy = EXCLUDED.accuracy,
                    precision_score = EXCLUDED.precision_score,
                    recall = EXCLUDED.recall,
                    roc_auc = EXCLUDED.roc_auc,
                    memory_mb = EXCLUDED.memory_mb,
                    cpu_utilization = EXCLUDED.cpu_utilization,
                    gpu_utilization = EXCLUDED.gpu_utilization,
                    roc_curve = EXCLUDED.roc_curve,
                    updated_at = NOW()
                """,
                (
                    run_id,
                    metrics["latency_ms"],
                    metrics["throughput_ops_per_sec"],
                    metrics["energy_joules_per_op"],
                    metrics["f1_score"],
                    metrics["false_positive_rate"],
                    metrics.get("accuracy"),
                    metrics.get("precision_score"),
                    metrics.get("recall"),
                    metrics.get("roc_auc"),
                    metrics.get("memory_mb"),
                    metrics.get("cpu_utilization"),
                    metrics.get("gpu_utilization"),
                    roc_json,
                ),
            )
        conn.commit()
