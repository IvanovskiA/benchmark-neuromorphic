"""MNSIM In-Memory Computing backend (PIM array adapter + IDS classifier)."""

from sklearn.neural_network import MLPClassifier
from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler

from metrics.accuracy import compute_metrics
from metrics.bundle import pack_metrics
from metrics.resources import profile_with_resources
from simulators.mnsim_adapter import mnsim_hardware_metrics


def run(X_train, y_train, X_test, y_test) -> dict:
    model = Pipeline([
        ("scaler", StandardScaler()),
        ("clf", MLPClassifier(hidden_layer_sizes=(64, 32), max_iter=200, random_state=42)),
    ])
    model.fit(X_train, y_train)

    hw = mnsim_hardware_metrics(X_train.shape[1], len(X_test))

    def predict_fn(X):
        return model.predict(X)

    perf = profile_with_resources(predict_fn, X_test, energy_factor=3e-12)
    # Replace wall-clock energy/latency with MNSIM PIM estimates (classification stays from MLP).
    perf["latency_ms"] = hw["latency_ms"]
    perf["throughput_ops_per_sec"] = hw["throughput_ops_per_sec"]
    perf["energy_joules_per_op"] = hw["energy_joules_per_op"]

    scores = model.predict_proba(X_test)[:, 1]
    accuracy = compute_metrics(y_test, perf["predictions"], scores)
    return pack_metrics(perf, accuracy, "mnsim_imc")
