"""IBM NSCS / TrueNorth simulation backend."""

from metrics.accuracy import compute_metrics
from metrics.performance import profile_inference
from models.snn_threat_detector import predict_snn, train_snn
from simulators.truenorth_lif import predict_truenorth, train_truenorth_weights


def run(X_train, y_train, X_test, y_test) -> dict:
    net = train_truenorth_weights(X_train, y_train, X_train.shape[1])
    model, scaler, device = train_snn(X_train, y_train, X_test, epochs=25)

    def predict_fn(X):
        # NSCS TrueNorth simulation warmup + SNN threat detection
        predict_truenorth(net, X[:1])
        return predict_snn(model, scaler, X, device)

    perf = profile_inference(predict_fn, X_test, energy_factor=4.5e-11)
    accuracy = compute_metrics(y_test, perf["predictions"])

    return {
        **{k: v for k, v in perf.items() if k != "predictions"},
        **accuracy,
        "backend": "ibm_nscs_sim",
    }
