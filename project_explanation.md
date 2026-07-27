# Објаснување на проектот — Benchmark Neuromorphic

---

## Прилог: Тројна верификација (Triple Check)

Пред да го објасниме проектот, еве што е проверено во кодот:


| Проверка                            | Статус              | Забелешка                                                                                                           |
| ----------------------------------- | ------------------- | ------------------------------------------------------------------------------------------------------------------- |
| Python скрипти и backend-и          | ✅ Исправно          | 4 backend-и (`cpu_baseline`, `gpu_baseline`, `lava_loihi`, `ibm_nscs`) се поврзани во `python/cli/run_benchmark.py` |
| SNN модел (snnTorch)                | ✅ Исправно          | `ThreatSNN` — rate-coded SNN со Leaky LIF нeurони, 25 временски чекори                                              |
| Симулации Lava / TrueNorth          | ✅ Логички исправно  | **CPU симулации**, не физички чипови — јасно е на Methodology страницата                                            |
| Dataset loaders (CICIDS, UNSW-NB15) | ✅ Исправно          | Читаат CSV од `/data/` или `storage/datasets/`; ако нема фајл → синтетички податоци                                 |
| Laravel → Python интеграција        | ✅ Исправно          | `BenchmarkService` стартува `run_benchmark.py --run-id {UUID}`                                                      |
| Python → PostgreSQL                 | ✅ Исправно          | `insert_metric()` и `update_run_status()` директно пишуваат во база                                                 |
| Laravel Dashboard                   | ✅ Исправно          | Чита метрики од `benchmark_metrics` и прикажува графикони                                                           |
| Формули за метрики                  | ✅ Математички точни | F1 и FPR од scikit-learn; latency/throughput од `time.perf_counter()`                                               |
| Energy (J/Op)                       | ⚠️ Проценето        | Не е мерен од hardware — формула: `elapsed × energy_factor` (различен factor по архитектура)                        |


**Заклучок:** Проектот е **логички целосен и функционален** за MVP/benchmark платформа. При одбрана, бидете искрени: neuromorphic delot е **симулација на CPU**, а енергијата е **теоретска проценка**, не мерење од физички чип.

---



## 1. ШТО Е ОВОЈ ПРОЕКТ?

Овој проект е **веб-платформа за споредба (benchmark)** на различни типови компјутерски архитектури при **откривање сајбер-напади** во мрежата.

**Проблемот:** Класичните компјутери (CPU/GPU) работат по **Von Neumann** принцип (одвоена меморија и процесор). **Neuromorphic** чипови (Intel Loihi, IBM TrueNorth) работат со **spiking neurons** — потрошуваат многу помалку енергија и можат да бидат побрзи за одредени AI задачи.

**Што решаваме:** Системот тренира/тестира модел за класификација „напад vs нормален сообраќај“ на **4 архитектури**, ги мери **5 метрики** (брзина, енергија, точност) и ги прикажува на Dashboard за лесна споредба.

---



## 2. КЛУЧНИ ПОИМИ



### 2.1 Што е Benchmark (Бенчмарк)?

**Benchmark** = стандардизиран тест за **мерење и споредба** на перформанси.

Зошто го правиме?

- За да видиме **која архитектура е побрза**, **потрошува помалку енергија** и **подобро детектира напади**
- За да имаме **бројки** (не само „ми се чини дека работи“), што можат да се прикажат на графикони и табели

Во нашиот проект, секое кликнување **„Start benchmark“** = еден benchmark run со избран dataset + architecture.

---



### 2.2 Што е Dataset (Сет на податоци)?

**Dataset** = збир од примероци (редови) со **features** (бројки за мрежниот сообраќај) и **label** (0 = нормално, 1 = напад).

Проектот поддржува **2 познати IDS (Intrusion Detection System) datasets**:

#### CICIDS (Canadian Institute for Cybersecurity IDS)


| Што е             | Детали                                                                                              |
| ----------------- | --------------------------------------------------------------------------------------------------- |
| **За што служи**  | Обука и тест на системи за откривање упади во мрежата                                               |
| **Што содржи**    | Записи за мрежен сообраќај: порт, траење на flow, број на пакети, брзина (bytes/s, packets/s), итн. |
| **Label**         | `benign` (нормално) или тип на напад → во кодот: 0 или 1                                            |
| **Каде е фајлот** | `storage/datasets/cicids.csv` или `/data/cicids.csv` (Docker)                                       |
| **Ако нема CSV**  | Генерираат се **3000 синтетички** примероци (за demo/MVP)                                           |




#### UNSW-NB15 (University of New South Wales)


| Што е             | Детали                                                                                     |
| ----------------- | ------------------------------------------------------------------------------------------ |
| **За што служи**  | Исто — детекција на мрежни упади и аномалии                                                |
| **Што содржи**    | Features како: duration, protocol, service, state, број на пакети, bytes, rate, load, итн. |
| **Label**         | `normal` → 0, сè друго (attack) → 1                                                        |
| **Каде е фајлот** | `storage/datasets/unsw_nb15.csv` или `/data/unsw_nb15.csv`                                 |
| **Ако нема CSV**  | **3000 синтетички** примероци                                                              |


**Важно за одбрана:** Системот **поддржува вистински CSV** од овие datasets. Ако ги ставите production фајловите на патеката, ќе се користат тие. Инаку, demo верзијата користи синтетички податоци со слична структура.

**Поделба:** 80% train (2400 samples) + 20% test (600 samples), со stratified split (пропорционално benign/attack).

---



### 2.3 Што е Architecture (Архитектура)?

**Architecture** = начин на кој се извршува AI моделот — на кој „хardware“ или симулатор.

Проектот има **4 архитектури**:

#### CPU Baseline (стандарден процесор — Von Neumann)


|                   |                                                                                   |
| ----------------- | --------------------------------------------------------------------------------- |
| **Што е**         | Класичен компјутерски CPU                                                         |
| **Модел**         | scikit-learn **MLP** (Multi-Layer Perceptron) — 2 скриени слоеви (64, 32 neurons) |
| **Библиотека**    | `sklearn.neural_network.MLPClassifier`                                            |
| **Улога**         | **Референца (baseline)** — „колку добро работи класичниот пристап“                |
| **Neuromorphic?** | Не (`is_neuromorphic = false`)                                                    |




#### GPU Baseline (графичка картичка)


|                   |                                                              |
| ----------------- | ------------------------------------------------------------ |
| **Што е**         | GPU за паралелно процесирање (ако има CUDA)                  |
| **Модел**         | PyTorch neural network (64→32→1, sigmoid)                    |
| **Улога**         | Побрз baseline за споредба со neuromorphic                   |
| **Fallback**      | Ако нема CUDA или PyTorch fail → автоматски **CPU Baseline** |
| **Neuromorphic?** | Не                                                           |




#### IBM NSCS / TrueNorth (не-Von Neumann — neuromorphic)


|                      |                                                                                         |
| -------------------- | --------------------------------------------------------------------------------------- |
| **Што е**            | IBM TrueNorth — neuromorphic чип со **spiking neurons** (LIF модел)                     |
| **Во проектот**      | **CPU симулација** — NumPy имплементација на LIF слоеви (`simulators/truenorth_lif.py`) |
| **Модел за точност** | snnTorch **SNN** (`ThreatSNN`) — rate-coded spiking network                             |
| **Neuromorphic?**    | Да (`is_neuromorphic = true`)                                                           |
| **Важно**            | Немаме физички TrueNorth чип — симулираме однесување на CPU                             |




#### Intel Lava / Loihi (не-Von Neumann — neuromorphic)


|                      |                                                                                                                          |
| -------------------- | ------------------------------------------------------------------------------------------------------------------------ |
| **Што е**            | Intel Loihi — neuromorphic чип; **Lava** е Intel framework за симулација                                                 |
| **Во проектот**      | **Loihi1SimCfg CPU simulation** — NumPy LIF (`simulators/lava_loihi_sim.py`); Lava runtime се probe-ира ако е инсталиран |
| **Модел за точност** | Ист snnTorch **SNN** (`ThreatSNN`)                                                                                       |
| **Neuromorphic?**    | Да                                                                                                                       |
| **Важно**            | Немаме физички Loihi чип — симулираме на CPU                                                                             |


**Зошто neuromorphic backend-ите користат SNN?**  
Spiking Neural Networks се природни за neuromorphic hardware — neurons „пукаат“ (spike) наместо да праќаат continuous бројки. Библиотеката **snnTorch** го имплементира тоа во PyTorch.

---



## 3. ШТО СЕ СЛУЧУВА КОГА ЌЕ КЛИКНАМ „START BENCHMARK“?



### Чекор 0: Што избираш на формата

На страницата **New Benchmark** (`/benchmarks/create`):

1. **Dataset** — CICIDS или UNSW-NB15
2. **Architecture** — една од 4-те
3. Клик **„Start benchmark“**

---



### Чекор 1: Laravel го кreira записот

**Фајл:** `app/Http/Controllers/BenchmarkController.php` → `store()`

1. Валидација: dataset_id и architecture_id мора да постојат
2. Кreira нов red во табелата `benchmark_runs`:
  - `id` = UUID (уникатен идентификатор)
  - `status` = `pending`
  - `dataset_id`, `architecture_id`

---



### Чекор 2: Laravel го стартува Python

**Фајл:** `app/Services/BenchmarkService.php`

1. Статус → `running`, `started_at` = сега
2. Стартува **subprocess** (надворешен процес):

```
python3 python/cli/run_benchmark.py --run-id {UUID}
```

1. Python процесот добива **environment variables**:
  - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (за PostgreSQL)
  - `CICIDS_PATH`, `UNSW_NB15_PATH` (патеки до CSV фајлови)
2. Laravel **чека** Python да заврши (синхроно, до 3600 секунди timeout)

---



### Чекор 3: Python CLI — главниот pipeline

**Фајл:** `python/cli/run_benchmark.py`

```
┌─────────────────────────────────────────────────────────┐
│ 1. update_run_status(run_id, "running")                 │
│ 2. fetch_run(run_id) → dataset_slug, architecture_slug│
│ 3. load_dataset(slug, path) → X_train, X_test, y_train, y_test │
│ 4. backend.run(X_train, y_train, X_test, y_test)      │
│ 5. insert_metric(run_id, metrics)                       │
│ 6. update_run_status(run_id, "completed")             │
└─────────────────────────────────────────────────────────┘
```

---



### Чекор 4: Од каде ги зема податоците?

**Фајл:** `python/datasets/loader.py` → `cicids_loader.py` / `unsw_nb15_loader.py`

1. Проверува дали постои CSV на:
  - `CICIDS_PATH` / `UNSW_NB15_PATH` (env)
  - или `/data/cicids.csv`, `/data/unsw_nb15.csv`
2. Ако **има CSV** → чита го, numeric колони → features, label → 0/1
3. Ако **нема CSV** → генерира синтетички 3000 samples
4. `train_test_split` → 80% train, 20% test

---



### Чекор 5: Како Python ги врти податоците низ моделот?

Зависи од **Architecture**:

#### A) CPU Baseline

1. **Train:** `MLPClassifier.fit(X_train, y_train)` — учи weights
2. **Inference:** `model.predict(X_test)` — предвидува label за секој test sample
3. Нема SNN — класичен MLP



#### B) GPU Baseline

1. **Train:** PyTorch model, 50 epochs, Adam optimizer
2. **Inference:** sigmoid output ≥ 0.5 → class 1, инаку 0
3. Нема SNN — класичен feedforward NN



#### C) Intel Lava / Loihi и D) IBM NSCS / TrueNorth

1. **Train SNN:** `train_snn()` — snnTorch `ThreatSNN`, 25 epochs, Adam, CrossEntropyLoss
  - Архитектура: Input → Linear(64) → Leaky LIF → Linear(2) → Leaky LIF
  - 25 временски чекори (spike accumulation)
2. **Train simulator weights:** heuristic weights за Lava/TrueNorth LIF (не gradient descent)
3. **Inference (**`predict_fn`**):**
  - Neuromorphic: прво **simulator warmup** (1 sample) — Lava или TrueNorth LIF
  - Пото: `predict_snn(model, scaler, X_test)` — SNN предвидува за **сите 600 test samples**
4. **Accuracy (F1, FPR)** се presmetuvaat од SNN predictions, не од simulator

**SNN flow (едноставно):**

```
Input features → normalize (StandardScaler)
→ за 25 чекори: neurons примаат input, „пукаат“ (spike) кога мембраната ≥ threshold
→ sum of output spikes → class 0 или 1
```

---



### Чекор 6: Што точно се пресметува — 5-те метрики

**Фајлови:** `python/metrics/performance.py`, `python/metrics/accuracy.py`

#### 1) Inference Latency (ms)

**Што мери:** Колку **мilliseconds** треба просечно за **1 sample** (1 inference).

**Формула:**

```
elapsed = време од start до end на predict_fn(X_test)  [секунди]
latency_ms = (elapsed / број_samples) × 1000
```

**Пример:** 600 samples за 0.3 sec → latency = 0.5 ms per sample

**Зошто CPU/GPU изгледаат „мали“:** Baseline inference е многу брз (microseconds) — на графиконот се користи log scale за да се видат.

---



#### 2) Throughput (ops/s)

**Што мери:** Колку **samples per second** може системот да procesира.

**Формула:**

```
throughput_ops_per_sec = број_samples / elapsed
```

**Забелешка:** Името „ops/s“ во кодот — во practice е **samples/sec** (една prediction = една операција per sample).

---



#### 3) Energy Consumption (J/Op — Joules per Operation)

**Што мери:** Проценета **енергија по операција** (Joule).

**Формула (проценка, не hardware мерење):**

```
energy_joules_per_op = elapsed × energy_factor
```

`energy_factor` е **различен по backend** (эмпiriчна константа):


| Backend            | energy_factor |
| ------------------ | ------------- |
| CPU Baseline       | 5×10⁻¹⁰       |
| GPU (CUDA)         | 2×10⁻⁹        |
| GPU (CPU fallback) | 8×10⁻¹⁰       |
| Lava / TrueNorth   | 4.5×10⁻¹¹     |


**Важно за одбрана:** Ова **не е** мерење од wattmeter на чип — е **теоретска проценка** пропорционална на време и тип архитектура. Neuromorphic factors се помали → помала projected energy.

---



#### 4) False Positive Rate (FPR)

**Што мери:** Колку често системот **погрешно** кажува „напад“ кога всушност е **нормален** сообраќај.

**Формула:**

```
FPR = FP / (FP + TN)
```

- **FP** (False Positive) = normal classified as attack
- **TN** (True Negative) = normal correctly classified as normal

**Помал FPR = подобро** (помалку лажни аларми).

---



#### 5) F1-Score

**Што мери:** Комбинирана **точност** на детекција — баланс помеѓу precision и recall.

**Формула:** scikit-learn `f1_score(y_true, y_pred)` — стандардна binary F1.

**Opseg:** 0 до 1. **Поголем F1 = подобро.**

**Зошто F1, не само accuracy?** При imbalanced datasets (повеќе normal отколку attack), accuracy може да изгледа добро ама моделот да пропушта напади. F1 е построг.

---



### Чекор 7: Како се запишува во базата?

**Фајл:** `python/db/writer.py` → `insert_metric()`

**Табела:** `benchmark_metrics`


| Колона                   | Тип            | Содржина       |
| ------------------------ | -------------- | -------------- |
| `benchmark_run_id`       | UUID           | поврзан со run |
| `latency_ms`             | NUMERIC(24,18) | latency        |
| `throughput_ops_per_sec` | NUMERIC(24,8)  | throughput     |
| `energy_joules_per_op`   | NUMERIC(30,20) | energy         |
| `f1_score`               | NUMERIC(20,15) | F1             |
| `false_positive_rate`    | NUMERIC(20,15) | FPR            |


**UPSERT:** Ако веќе постои metric за тој run → UPDATE (не duplicate).

**Статус:** `benchmark_runs.status` → `completed`, `finished_at` = NOW()

---



### Чекор 8: Како Laravel ги прикажува на Dashboard?

**Фајл:** `app/Http/Controllers/BenchmarkController.php`

1. **Dashboard** (`/benchmarks`):
  - `BenchmarkMetric::avg()` → просечни F1, latency, energy (stat cards)
  - `buildDashboardCharts()` → групира completed runs по architecture → Chart.js bar charts
2. **History** (`/benchmarks/history`):
  - Paginated листа на сите runs + F1, latency
3. **Show** (`/benchmarks/{uuid}`):
  - Детали за еден run + 5 stat cards
4. **Charts** (`/benchmarks/charts`):
  - Филтри по dataset/architecture
  - Comparison table: neuromorphic vs baseline по dataset

**Frontend:** Blade templates + Chart.js (графикони), `MetricsFormat::card()` за приказ на 4 decimal places во cards.

---



## Дијаграм на целата архитектура

```
┌──────────────┐     POST /benchmarks      ┌─────────────────┐
│   Browser    │ ────────────────────────► │ Laravel (PHP)   │
│  Dashboard   │                           │ BenchmarkService│
└──────────────┘                           └────────┬────────┘
       ▲                                            │
       │                                            │ Process::run()
       │                                            ▼
       │                                   ┌─────────────────┐
       │                                   │ Python CLI      │
       │                                   │ run_benchmark.py│
       │                                   └────────┬────────┘
       │                                            │
       │              ┌─────────────────────────────┼─────────────────────────────┐
       │              │                             │                             │
       │              ▼                             ▼                             ▼
       │     ┌────────────────┐           ┌────────────────┐           ┌────────────────┐
       │     │ Dataset CSV    │           │ Backend        │           │ PostgreSQL     │
       │     │ CICIDS/UNSW    │           │ CPU/GPU/Lava/  │           │ benchmark_runs │
       │     └────────────────┘           │ IBM            │           │ benchmark_     │
       │                                  └────────┬───────┘           │ metrics        │
       │                                           │                   └────────▲───────┘
       │                                           │ insert_metric()            │
       │                                           └────────────────────────────┘
       │
       └──────── GET /benchmarks ──── Eloquent read ──── Chart.js graphs
```

---



## Краток речник за одбрана


| Термин           | Едноставно                                                        |
| ---------------- | ----------------------------------------------------------------- |
| **SNN**          | Spiking Neural Network — neurons комуницираат со импулси (spikes) |
| **LIF**          | Leaky Integrate-and-Fire — математички модел на neuron            |
| **Rate coding**  | Input се повторува низ повеќе временски чекори                    |
| **Baseline**     | Референца за споредба (CPU/GPU)                                   |
| **Neuromorphic** | Hardware што имитира мозочни neurons                              |
| **IDS**          | Intrusion Detection System — систем за детекција на упади         |
| **Von Neumann**  | Класична архитектура: CPU + RAM одделно                           |


---



## Што да кажеш на profesorkata (3 реченици)

1. „Платформата споредува 4 архитектури — 2 baseline (CPU/GPU MLP) и 2 neuromorphic симулации (Intel Lava/Loihi и IBM TrueNorth) — на CICIDS и UNSW-NB15 datasets за binary threat detection.“
2. „Laravel Dashboard стартува Python pipeline кој тренира модел, мери latency, throughput, energy, F1 и FPR, и резултатите ги зачувува во PostgreSQL.“
3. „Бидејќи немаме физички neuromorphic чипови, користиме snnTorch SNN за classification и CPU симулатори за LIF dynamics — energy е проценета, не мерена од hardware.“

---

*Документ генериран по triple-check верификација на codebase. Последна проверка: јули 2026.*