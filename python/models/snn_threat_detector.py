"""Spiking Neural Network threat detector (snnTorch)."""

from __future__ import annotations

import numpy as np
import torch
import torch.nn as nn
from sklearn.preprocessing import StandardScaler
import snntorch as snn
from snntorch import surrogate


class ThreatSNN(nn.Module):
    """Rate-coded SNN for binary threat classification."""

    def __init__(self, input_size: int, hidden: int = 64, num_steps: int = 25):
        super().__init__()
        self.num_steps = num_steps
        self.fc1 = nn.Linear(input_size, hidden)
        self.lif1 = snn.Leaky(beta=0.9, spike_grad=surrogate.fast_sigmoid())
        self.fc2 = nn.Linear(hidden, 2)
        self.lif2 = snn.Leaky(beta=0.9, spike_grad=surrogate.fast_sigmoid())

    def forward(self, x: torch.Tensor) -> torch.Tensor:
        mem1 = self.lif1.init_leaky()
        mem2 = self.lif2.init_leaky()
        spk2_rec = []

        for _ in range(self.num_steps):
            cur1 = self.fc1(x)
            spk1, mem1 = self.lif1(cur1, mem1)
            cur2 = self.fc2(spk1)
            spk2, mem2 = self.lif2(cur2, mem2)
            spk2_rec.append(spk2)

        return torch.stack(spk2_rec).sum(dim=0)


def _to_tensor(X: np.ndarray, device: torch.device) -> torch.Tensor:
    return torch.tensor(X, dtype=torch.float32, device=device)


def train_snn(
    X_train: np.ndarray,
    y_train: np.ndarray,
    X_test: np.ndarray,
    *,
    num_steps: int = 25,
    epochs: int = 30,
    lr: float = 1e-3,
    random_state: int = 42,
) -> tuple[ThreatSNN, StandardScaler, torch.device]:
    torch.manual_seed(random_state)
    device = torch.device("cpu")

    scaler = StandardScaler()
    X_train_s = scaler.fit_transform(X_train)
    scaler.transform(X_test)

    model = ThreatSNN(X_train.shape[1], num_steps=num_steps).to(device)
    optimizer = torch.optim.Adam(model.parameters(), lr=lr)
    loss_fn = nn.CrossEntropyLoss()

    X_train_t = _to_tensor(X_train_s, device)
    y_train_t = torch.tensor(y_train, dtype=torch.long, device=device)

    model.train()
    for _ in range(epochs):
        optimizer.zero_grad()
        logits = model(X_train_t)
        loss = loss_fn(logits, y_train_t)
        loss.backward()
        optimizer.step()

    return model, scaler, device


def predict_snn(
    model: ThreatSNN,
    scaler: StandardScaler,
    X: np.ndarray,
    device: torch.device,
) -> np.ndarray:
    logits = snn_logits(model, scaler, X, device)
    return logits.argmax(dim=1).cpu().numpy()


def snn_logits(model: ThreatSNN, scaler: StandardScaler, X: np.ndarray, device: torch.device):
    model.eval()
    with torch.no_grad():
        X_t = _to_tensor(scaler.transform(X), device)
        return model(X_t)


def snn_positive_scores(model: ThreatSNN, scaler: StandardScaler, X: np.ndarray, device: torch.device) -> np.ndarray:
    logits = snn_logits(model, scaler, X, device)
    probs = torch.softmax(logits, dim=1)
    return probs[:, 1].cpu().numpy()
