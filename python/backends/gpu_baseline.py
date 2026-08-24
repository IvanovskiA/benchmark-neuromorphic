"""GPU baseline backend (falls back to CPU if GPU unavailable)."""

from backends.cpu_baseline import run as cpu_run
from metrics.accuracy import compute_metrics
from metrics.bundle import pack_metrics
from metrics.resources import profile_with_resources


def run(X_train, y_train, X_test, y_test) -> dict:
    try:
        import torch
        import torch.nn as nn
        from sklearn.preprocessing import StandardScaler

        device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
        scaler = StandardScaler()
        X_train_s = scaler.fit_transform(X_train)
        X_test_s = scaler.transform(X_test)

        X_train_t = torch.tensor(X_train_s, dtype=torch.float32, device=device)
        y_train_t = torch.tensor(y_train, dtype=torch.float32, device=device).unsqueeze(1)

        model = nn.Sequential(
            nn.Linear(X_train.shape[1], 64),
            nn.ReLU(),
            nn.Linear(64, 32),
            nn.ReLU(),
            nn.Linear(32, 1),
            nn.Sigmoid(),
        ).to(device)

        optimizer = torch.optim.Adam(model.parameters(), lr=0.001)
        loss_fn = nn.BCELoss()

        model.train()
        for _ in range(50):
            optimizer.zero_grad()
            loss = loss_fn(model(X_train_t), y_train_t)
            loss.backward()
            optimizer.step()

        scores_holder = {}

        def predict_fn(X):
            model.eval()
            with torch.no_grad():
                X_t = torch.tensor(scaler.transform(X), dtype=torch.float32, device=device)
                probs = model(X_t).cpu().numpy().flatten()
                scores_holder["scores"] = probs
                return (probs >= 0.5).astype(int)

        energy = 2e-9 if device.type == "cuda" else 8e-10
        perf = profile_with_resources(predict_fn, X_test, energy_factor=energy)
        accuracy = compute_metrics(y_test, perf["predictions"], scores_holder.get("scores"))
        label = "gpu_baseline" if device.type == "cuda" else "gpu_baseline_cpu_fallback"
        return pack_metrics(perf, accuracy, label)
    except Exception:
        result = cpu_run(X_train, y_train, X_test, y_test)
        result["backend"] = "gpu_baseline_cpu_fallback"
        return result
