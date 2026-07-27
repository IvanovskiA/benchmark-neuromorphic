"""IBM TrueNorth / NSCS-style leaky integrate-and-fire simulation."""

from __future__ import annotations

import numpy as np


class TrueNorthLIFLayer:
    """Simplified TrueNorth neuron model (binary synapses, leak, refractory)."""

    def __init__(self, n_neurons: int, threshold: float = 1.0, leak: float = 0.0078125):
        self.n_neurons = n_neurons
        self.threshold = threshold
        self.leak = leak
        self.membrane = np.zeros(n_neurons, dtype=np.float32)

    def step(self, input_current: np.ndarray) -> np.ndarray:
        self.membrane *= 1.0 - self.leak
        self.membrane += input_current
        spikes = (self.membrane >= self.threshold).astype(np.int8)
        self.membrane[spikes > 0] = 0.0
        return spikes


class TrueNorthNetwork:
    """Two-layer TrueNorth-style SNN for threat classification."""

    def __init__(self, input_size: int, hidden: int = 64, num_steps: int = 25):
        self.input_size = input_size
        self.hidden = hidden
        self.num_steps = num_steps
        self.w1 = np.zeros((input_size, hidden), dtype=np.float32)
        self.w2 = np.zeros((hidden, 2), dtype=np.float32)
        self.hidden_layer = TrueNorthLIFLayer(hidden)
        self.output_layer = TrueNorthLIFLayer(2, threshold=0.8)

    def forward(self, x: np.ndarray) -> int:
        out_spikes = np.zeros(2, dtype=np.float32)

        for _ in range(self.num_steps):
            h_in = x @ self.w1
            h_spk = self.hidden_layer.step(h_in.astype(np.float32))
            o_in = h_spk.astype(np.float32) @ self.w2
            o_spk = self.output_layer.step(o_in.astype(np.float32))
            out_spikes += o_spk

        return 1 if out_spikes[1] > out_spikes[0] else 0


def train_truenorth_weights(
    X_train: np.ndarray,
    y_train: np.ndarray,
    input_size: int,
    hidden: int = 64,
) -> TrueNorthNetwork:
    """Build TrueNorth binary weights from feature-label correlations."""
    from sklearn.preprocessing import StandardScaler

    scaler = StandardScaler()
    X_s = scaler.fit_transform(X_train)
    net = TrueNorthNetwork(input_size, hidden=hidden)

    pos = X_s[y_train == 1].mean(axis=0) if (y_train == 1).any() else np.zeros(input_size)
    neg = X_s[y_train == 0].mean(axis=0) if (y_train == 0).any() else np.zeros(input_size)
    signal = pos - neg

    for j in range(hidden):
        threshold = np.percentile(signal, 35 + (j * 40 // hidden))
        net.w1[:, j] = (signal >= threshold).astype(np.float32)

    attack_h = X_s[y_train == 1] @ net.w1 if (y_train == 1).any() else np.zeros((1, hidden))
    benign_h = X_s[y_train == 0] @ net.w1 if (y_train == 0).any() else np.zeros((1, hidden))
    attack_score = attack_h.mean(axis=0) if len(attack_h) else np.zeros(hidden)
    benign_score = benign_h.mean(axis=0) if len(benign_h) else np.zeros(hidden)

    net.w2[:, 1] = (attack_score >= benign_score).astype(np.float32)
    net.w2[:, 0] = 1.0 - net.w2[:, 1]

    net._scaler = scaler
    return net


def predict_truenorth(net: TrueNorthNetwork, X: np.ndarray) -> np.ndarray:
    scaler = getattr(net, "_scaler", None)
    if scaler is not None:
        X = scaler.transform(X)

    preds = []
    for row in X:
        row_norm = row.astype(np.float32)
        row_norm = np.clip(row_norm, 0, None)
        if row_norm.max() > 0:
            row_norm = row_norm / row_norm.max()
        preds.append(net.forward(row_norm))
    return np.array(preds)
