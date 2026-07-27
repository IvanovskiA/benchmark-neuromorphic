"""Accuracy and classification metrics."""

from sklearn.metrics import f1_score, confusion_matrix


def compute_metrics(y_true, y_pred) -> dict:
    f1 = float(f1_score(y_true, y_pred, zero_division=0))
    tn, fp, fn, tp = confusion_matrix(y_true, y_pred, labels=[0, 1]).ravel()
    fpr = float(fp / (fp + tn)) if (fp + tn) > 0 else 0.0

    return {
        "f1_score": f1,
        "false_positive_rate": fpr,
    }
