"""CPU baseline backend (sklearn MLP — Von Neumann reference)."""

from sklearn.neural_network import MLPClassifier
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler

from metrics.accuracy import compute_metrics
from metrics.performance import profile_inference


def _build_baseline():
    return Pipeline([
        ("scaler", StandardScaler()),
        ("clf", MLPClassifier(hidden_layer_sizes=(64, 32), max_iter=200, random_state=42)),
    ])


def run(X_train, y_train, X_test, y_test) -> dict:
    model = _build_baseline()
    model.fit(X_train, y_train)

    perf = profile_inference(model.predict, X_test, energy_factor=5e-10)
    accuracy = compute_metrics(y_test, perf["predictions"])

    return {
        **{k: v for k, v in perf.items() if k != "predictions"},
        **accuracy,
        "backend": "cpu_baseline",
    }
