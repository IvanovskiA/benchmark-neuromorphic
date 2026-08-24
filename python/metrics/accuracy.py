"""Accuracy and classification metrics."""

from __future__ import annotations

import numpy as np
from sklearn.metrics import (
    accuracy_score,
    confusion_matrix,
    f1_score,
    precision_score,
    recall_score,
    roc_auc_score,
    roc_curve,
)


def compute_metrics(y_true, y_pred, y_score=None) -> dict:
    y_true = np.asarray(y_true).astype(int).ravel()
    y_pred = np.asarray(y_pred).astype(int).ravel()

    f1 = float(f1_score(y_true, y_pred, zero_division=0))
    tn, fp, fn, tp = confusion_matrix(y_true, y_pred, labels=[0, 1]).ravel()
    fpr = float(fp / (fp + tn)) if (fp + tn) > 0 else 0.0

    scores = y_score
    if scores is None:
        scores = y_pred.astype(float)
    scores = np.asarray(scores, dtype=float).ravel()

    roc_auc = 0.0
    curve = {"fpr": [0.0, 1.0], "tpr": [0.0, 1.0]}
    if len(np.unique(y_true)) > 1:
        try:
            roc_auc = float(roc_auc_score(y_true, scores))
            fpr_pts, tpr_pts, _ = roc_curve(y_true, scores)
            curve = {
                "fpr": [float(v) for v in fpr_pts],
                "tpr": [float(v) for v in tpr_pts],
            }
        except ValueError:
            roc_auc = 0.0

    return {
        "f1_score": f1,
        "false_positive_rate": fpr,
        "accuracy": float(accuracy_score(y_true, y_pred)),
        "precision_score": float(precision_score(y_true, y_pred, zero_division=0)),
        "recall": float(recall_score(y_true, y_pred, zero_division=0)),
        "roc_auc": roc_auc,
        "roc_curve": curve,
    }
