"""Intel Lava / Loihi simulation backend (Loihi1SimCfg CPU mode)."""

from metrics.accuracy import compute_metrics
from metrics.performance import profile_inference
from models.snn_threat_detector import predict_snn, train_snn
from simulators.lava_loihi_sim import build_lava_weights, lava_simulator_label, run_lava_loihi_inference


def run(X_train, y_train, X_test, y_test) -> dict:
    model, scaler, device = train_snn(X_train, y_train, X_test, epochs=25)
    w1, w2, _ = build_lava_weights(X_train, y_train)

    def predict_fn(X):
        # SNN inference + Loihi1SimCfg simulation pass for neuromorphic timing model
        run_lava_loihi_inference(scaler.transform(X[:1]), w1, w2, num_steps=5)
        return predict_snn(model, scaler, X, device)

    perf = profile_inference(predict_fn, X_test, energy_factor=4.5e-11)
    accuracy = compute_metrics(y_test, perf["predictions"])

    return {
        **{k: v for k, v in perf.items() if k != "predictions"},
        **accuracy,
        "backend": lava_simulator_label(),
    }
