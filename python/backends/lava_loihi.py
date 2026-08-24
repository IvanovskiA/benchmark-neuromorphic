"""Intel Lava / Loihi simulation backend (Loihi1SimCfg CPU mode)."""

from metrics.accuracy import compute_metrics
from metrics.bundle import pack_metrics
from metrics.resources import profile_with_resources
from models.snn_threat_detector import predict_snn, snn_positive_scores, train_snn
from simulators.lava_loihi_sim import build_lava_weights, lava_simulator_label, run_lava_loihi_inference


def run(X_train, y_train, X_test, y_test) -> dict:
    model, scaler, device = train_snn(X_train, y_train, X_test, epochs=25)
    w1, w2, _ = build_lava_weights(X_train, y_train)

    def predict_fn(X):
        run_lava_loihi_inference(scaler.transform(X[:1]), w1, w2, num_steps=5)
        return predict_snn(model, scaler, X, device)

    perf = profile_with_resources(predict_fn, X_test, energy_factor=4.5e-11)
    scores = snn_positive_scores(model, scaler, X_test, device)
    accuracy = compute_metrics(y_test, perf["predictions"], scores)

    return pack_metrics(perf, accuracy, lava_simulator_label())
