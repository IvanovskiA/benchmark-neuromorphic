--
-- PostgreSQL database dump (pgAdmin 4)
-- Database: benchmark
--
-- Import во pgAdmin 4:
-- 1. Create Database -> име: benchmark (Encoding UTF8)
-- 2. Query Tool врз таа база
-- 3. Open File -> database/database.sql -> Execute (F5)
--
-- Не користи Restore (.backup). Ова е обичен SQL скрипт.
--

-- Dumped from database version 16.14
-- Dumped by pg_dump version 16.14

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE IF EXISTS ONLY public.benchmark_runs DROP CONSTRAINT IF EXISTS benchmark_runs_dataset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.benchmark_runs DROP CONSTRAINT IF EXISTS benchmark_runs_architecture_id_foreign;
ALTER TABLE IF EXISTS ONLY public.benchmark_metrics DROP CONSTRAINT IF EXISTS benchmark_metrics_benchmark_run_id_foreign;
DROP INDEX IF EXISTS public.benchmark_runs_status_created_at_index;
ALTER TABLE IF EXISTS ONLY public.migrations DROP CONSTRAINT IF EXISTS migrations_pkey;
ALTER TABLE IF EXISTS ONLY public.datasets DROP CONSTRAINT IF EXISTS datasets_slug_unique;
ALTER TABLE IF EXISTS ONLY public.datasets DROP CONSTRAINT IF EXISTS datasets_pkey;
ALTER TABLE IF EXISTS ONLY public.benchmark_runs DROP CONSTRAINT IF EXISTS benchmark_runs_pkey;
ALTER TABLE IF EXISTS ONLY public.benchmark_metrics DROP CONSTRAINT IF EXISTS benchmark_metrics_pkey;
ALTER TABLE IF EXISTS ONLY public.benchmark_metrics DROP CONSTRAINT IF EXISTS benchmark_metrics_benchmark_run_id_unique;
ALTER TABLE IF EXISTS ONLY public.architectures DROP CONSTRAINT IF EXISTS architectures_slug_unique;
ALTER TABLE IF EXISTS ONLY public.architectures DROP CONSTRAINT IF EXISTS architectures_pkey;
ALTER TABLE IF EXISTS public.migrations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.datasets ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.benchmark_metrics ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.architectures ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE IF EXISTS public.migrations_id_seq;
DROP TABLE IF EXISTS public.migrations;
DROP SEQUENCE IF EXISTS public.datasets_id_seq;
DROP TABLE IF EXISTS public.datasets;
DROP TABLE IF EXISTS public.benchmark_runs;
DROP SEQUENCE IF EXISTS public.benchmark_metrics_id_seq;
DROP TABLE IF EXISTS public.benchmark_metrics;
DROP SEQUENCE IF EXISTS public.architectures_id_seq;
DROP TABLE IF EXISTS public.architectures;
SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: architectures; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.architectures (
    id bigint NOT NULL,
    slug character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    is_neuromorphic boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: architectures_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.architectures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: architectures_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.architectures_id_seq OWNED BY public.architectures.id;


--
-- Name: benchmark_metrics; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.benchmark_metrics (
    id bigint NOT NULL,
    benchmark_run_id uuid NOT NULL,
    latency_ms numeric(24,18),
    throughput_ops_per_sec numeric(24,8),
    energy_joules_per_op numeric(30,20),
    f1_score numeric(20,15),
    false_positive_rate numeric(20,15),
    accuracy numeric(20,15),
    precision_score numeric(20,15),
    recall numeric(20,15),
    roc_auc numeric(20,15),
    memory_mb numeric(24,8),
    cpu_utilization numeric(12,4),
    gpu_utilization numeric(12,4),
    roc_curve json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: benchmark_metrics_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.benchmark_metrics_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: benchmark_metrics_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.benchmark_metrics_id_seq OWNED BY public.benchmark_metrics.id;


--
-- Name: benchmark_runs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.benchmark_runs (
    id uuid NOT NULL,
    dataset_id bigint NOT NULL,
    architecture_id bigint NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    started_at timestamp(0) without time zone,
    finished_at timestamp(0) without time zone,
    error_message text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: datasets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.datasets (
    id bigint NOT NULL,
    slug character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: datasets_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.datasets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: datasets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.datasets_id_seq OWNED BY public.datasets.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: architectures id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.architectures ALTER COLUMN id SET DEFAULT nextval('public.architectures_id_seq'::regclass);


--
-- Name: benchmark_metrics id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.benchmark_metrics ALTER COLUMN id SET DEFAULT nextval('public.benchmark_metrics_id_seq'::regclass);


--
-- Name: datasets id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datasets ALTER COLUMN id SET DEFAULT nextval('public.datasets_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Data for Name: architectures; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.architectures (id, slug, name, is_neuromorphic, created_at, updated_at, deleted_at) VALUES (1, 'lava_loihi', 'Intel Lava / Loihi', true, '2026-07-25 17:23:37', '2026-07-25 17:23:37', NULL);
INSERT INTO public.architectures (id, slug, name, is_neuromorphic, created_at, updated_at, deleted_at) VALUES (2, 'ibm_nscs', 'IBM NSCS / TrueNorth', true, '2026-07-25 17:23:37', '2026-07-25 17:23:37', NULL);
INSERT INTO public.architectures (id, slug, name, is_neuromorphic, created_at, updated_at, deleted_at) VALUES (3, 'cpu_baseline', 'CPU Baseline', false, '2026-07-25 17:23:37', '2026-07-25 17:23:37', NULL);
INSERT INTO public.architectures (id, slug, name, is_neuromorphic, created_at, updated_at, deleted_at) VALUES (4, 'gpu_baseline', 'GPU Baseline', false, '2026-07-25 17:23:37', '2026-07-25 17:23:37', NULL);
INSERT INTO public.architectures (id, slug, name, is_neuromorphic, created_at, updated_at, deleted_at) VALUES (5, 'mnsim_imc', 'MNSIM IMC', false, '2026-08-24 19:04:37', '2026-08-24 19:04:37', NULL);


--
-- Data for Name: benchmark_metrics; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (1, '97aa3683-28ac-4b38-abf4-0d695cb98e31', 0.002200000000000000, 448689.54590000, 0.00000000000000000000, 0.808184000000000, 0.165000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 17:48:16', '2026-07-25 17:48:16', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (2, 'c0743109-55bb-4d1f-aec6-8285b188a232', 0.001700000000000000, 577810.07330000, 0.00000000000000000000, 0.869565000000000, 0.068182000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 17:57:00', '2026-07-25 17:57:00', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (3, '1cf7df52-b46d-41db-86d5-62bfc713cb74', 0.001700000000000000, 579890.83570000, 0.00000000000000000000, 0.807388000000000, 0.130000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 17:59:42', '2026-07-25 17:59:42', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (4, '6cc0170d-550c-4083-8771-0874e24bb97c', 0.040700000000000000, 24542.97000000, 0.00000000000000000000, 0.674847000000000, 0.080000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:00:07', '2026-07-25 18:00:07', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (5, '5870d533-72ed-41c6-8afb-2cb92590d723', 0.849500000000000000, 1177.13380000, 0.00000000000000000000, 0.674847000000000, 0.080000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:00:26', '2026-07-25 18:00:26', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (6, 'd8b6e2e6-c44e-47eb-9071-aeee2a18e635', 0.044100000000000000, 22681.98870000, 0.00000000000000000000, 0.674847000000000, 0.080000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:00:48', '2026-07-25 18:00:48', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (7, '9cce84b3-7b30-45b5-a61e-9c5052b23abc', 0.002600000000000000, 390329.96570000, 0.00000000000000000000, 0.911602000000000, 0.077273000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:01:29', '2026-07-25 18:01:29', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (8, '905a9510-190a-4519-8b61-adf8e59f2608', 0.002200000000000000, 451025.91430000, 0.00000000000000000000, 0.873563000000000, 0.072727000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:01:43', '2026-07-25 18:01:43', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (9, 'b1b62852-98a4-4eaf-bc3a-295584e66fbd', 0.042000000000000000, 23801.16080000, 0.00000000000000000000, 0.615970000000000, 0.009091000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:01:57', '2026-07-25 18:01:57', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (10, '306e5b4d-5c5e-4a68-8d25-74415391417d', 0.631000000000000000, 1584.80470000, 0.00000000000000000000, 0.615970000000000, 0.009091000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:02:15', '2026-07-25 18:02:15', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (11, 'cdcf6f60-04b4-41a1-b7fa-393622b4a7dd', 0.002390182498857030, 418378.09475979, 0.00000000000047803650, 0.808184143222506, 0.165000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:08:00', '2026-07-25 18:08:00', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (12, '316e13ae-fba0-434a-9b3d-1b74f9199ea0', 0.002697255004022740, 370747.29623583, 0.00000000000086312160, 0.828496042216359, 0.110000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:08:10', '2026-07-25 18:08:10', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (13, 'a2951678-014d-45af-be11-c0eef26559ee', 0.036253867501727700, 27583.26404631, 0.00000000000065256962, 0.674846625766871, 0.080000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:08:29', '2026-07-25 18:08:29', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (14, '4d3503b4-f230-408a-ab02-5234d395441d', 0.506953534995773000, 1972.56736756, 0.00000000000912516363, 0.674846625766871, 0.080000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:08:48', '2026-07-25 18:08:48', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (15, '388f6a0b-3fa0-47e3-b982-53f8e2e906e3', 0.002581384997029090, 387388.94087899, 0.00000000000051627700, 0.911602209944751, 0.077272727272727, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:09:18', '2026-07-25 18:09:18', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (16, '33c170cf-9857-4fbc-8799-2c20d97b7fba', 0.002457990003676970, 406836.47960491, 0.00000000000078655680, 0.859649122807018, 0.068181818181818, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:09:29', '2026-07-25 18:09:29', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (17, '072b0f0d-1b56-44c5-b8ed-19421c60f761', 0.034236832498209000, 29208.30950271, 0.00000000000061626298, 0.615969581749049, 0.009090909090909, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:09:42', '2026-07-25 18:09:42', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (18, '985c9392-f957-4b08-a6ed-2058e6738d49', 0.507192547493105000, 1971.63780293, 0.00000000000912946585, 0.615969581749049, 0.009090909090909, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 18:09:55', '2026-07-25 18:09:55', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (19, 'cd0e7c5e-3206-4046-af52-aec6a24ff6f3', 0.006412587500221889, 155943.29121675, 0.00000000000128251750, 0.808184143222506, 0.165000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-27 17:41:03', '2026-07-27 17:41:03', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (20, '362050bc-b0df-422a-b204-bca6898dbcef', 0.002886907499828340, 346391.42406172, 0.00000000000057738150, 0.911602209944751, 0.077272727272727, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-27 17:44:25', '2026-07-27 17:44:25', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (21, '2ae91af0-d20b-4e8c-802c-df5377895dbf', 0.003482592499892689, 287142.40900445, 0.00000000000069651850, 0.808184143222506, 0.165000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-27 17:47:31', '2026-07-27 17:47:31', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (22, 'e726a26c-60fb-4004-a514-1fd7bd465bcd', 0.011234694999870953, 89009.98202546, 0.00000000000224693900, 0.808184143222506, 0.165000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:14:47', '2026-07-28 20:14:47', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (23, 'd6e866a3-b681-468f-8132-173569dfa6d5', 0.001728837499967995, 578423.36253032, 0.00000000000055322800, 0.813471502590674, 0.145000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:15:23', '2026-07-28 20:15:23', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (24, 'fcebe0c3-2b08-45c5-b50c-df94e9c2fb83', 0.040677987499861956, 24583.32040157, 0.00000000000073220377, 0.674846625766871, 0.080000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:15:55', '2026-07-28 20:15:55', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (25, 'aa2e74a9-410f-4de9-9472-3037908b8c9f', 0.772760609999636500, 1294.06181819, 0.00000000001390969098, 0.674846625766871, 0.080000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:16:22', '2026-07-28 20:16:22', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (26, '4b59be52-61b8-4495-94d4-97826f4acb1e', 0.002581059999897661, 387437.71940197, 0.00000000000051621200, 0.911602209944751, 0.077272727272727, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:16:50', '2026-07-28 20:16:50', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (27, '995d2361-8ee7-42ac-a32f-166f52943b04', 0.002167022499861560, 461462.67519783, 0.00000000000043340450, 0.808184143222506, 0.165000000000000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:17:53', '2026-07-28 20:17:53', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (28, 'cc28f034-e665-4c3d-bd56-9e3eed49e7cf', 0.002624437499889609, 381034.03111793, 0.00000000000052488750, 0.911602209944751, 0.077272727272727, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:19:01', '2026-07-28 20:19:01', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (29, 'a205385e-ad6f-4163-aa9a-a7b79286170c', 0.002100642499271999, 476044.82930654, 0.00000000000067220560, 0.850931677018634, 0.022727272727273, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:19:41', '2026-07-28 20:19:41', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (30, '6db240eb-5748-4f6c-a7dd-b0f89df04649', 0.047133387499798120, 21216.38297278, 0.00000000000084840097, 0.615969581749050, 0.009090909090909, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:20:20', '2026-07-28 20:20:20', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (31, 'f5854c19-29b2-4cae-88d2-b55de3d90b47', 0.486844614999881740, 2054.04346518, 0.00000000000876320307, 0.615969581749050, 0.009090909090909, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-28 20:20:53', '2026-07-28 20:20:53', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (32, 'aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee', 0.006018149999960087, 166164.02050574, 0.00000000000120363000, 0.808184143222506, 0.165000000000000, 0.812500000000000, 0.827225130890052, 0.790000000000000, 0.897925000000000, 298.66406250, 46.3000, 0.0000, '{"fpr": [0.0, 0.0, 0.0, 0.005, 0.005, 0.01, 0.01, 0.015, 0.015, 0.025, 0.025, 0.03, 0.03, 0.035, 0.035, 0.04, 0.04, 0.045, 0.045, 0.05, 0.05, 0.055, 0.055, 0.06, 0.06, 0.065, 0.065, 0.07, 0.07, 0.1, 0.1, 0.105, 0.105, 0.125, 0.125, 0.13, 0.13, 0.135, 0.135, 0.14, 0.14, 0.145, 0.145, 0.15, 0.15, 0.16, 0.16, 0.165, 0.165, 0.17, 0.17, 0.185, 0.185, 0.195, 0.195, 0.205, 0.205, 0.21, 0.21, 0.215, 0.215, 0.23, 0.23, 0.24, 0.24, 0.25, 0.25, 0.26, 0.26, 0.27, 0.27, 0.34, 0.34, 0.355, 0.355, 0.38, 0.38, 0.395, 0.395, 0.4, 0.4, 0.41, 0.41, 0.42, 0.42, 0.495, 0.495, 0.515, 0.515, 0.53, 0.53, 0.54, 0.54, 0.585, 0.585, 0.695, 0.695, 0.7, 0.7, 0.745, 0.745, 0.85, 0.85, 1.0], "tpr": [0.0, 0.005, 0.18, 0.18, 0.35, 0.35, 0.425, 0.425, 0.445, 0.445, 0.48, 0.48, 0.485, 0.485, 0.515, 0.515, 0.53, 0.53, 0.575, 0.575, 0.58, 0.58, 0.6, 0.6, 0.64, 0.64, 0.695, 0.695, 0.72, 0.72, 0.725, 0.725, 0.735, 0.735, 0.745, 0.745, 0.755, 0.755, 0.765, 0.765, 0.775, 0.775, 0.78, 0.78, 0.785, 0.785, 0.79, 0.79, 0.805, 0.805, 0.81, 0.81, 0.815, 0.815, 0.82, 0.82, 0.83, 0.83, 0.85, 0.85, 0.855, 0.855, 0.86, 0.86, 0.865, 0.865, 0.885, 0.885, 0.895, 0.895, 0.9, 0.9, 0.905, 0.905, 0.915, 0.915, 0.92, 0.92, 0.925, 0.925, 0.93, 0.93, 0.935, 0.935, 0.94, 0.94, 0.945, 0.945, 0.95, 0.95, 0.955, 0.955, 0.965, 0.965, 0.975, 0.975, 0.98, 0.98, 0.985, 0.985, 0.99, 0.99, 1.0, 1.0]}', '2026-08-24 19:10:39', '2026-08-24 19:10:39', NULL);
INSERT INTO public.benchmark_metrics (id, benchmark_run_id, latency_ms, throughput_ops_per_sec, energy_joules_per_op, f1_score, false_positive_rate, accuracy, precision_score, recall, roc_auc, memory_mb, cpu_utilization, gpu_utilization, roc_curve, created_at, updated_at, deleted_at) VALUES (34, '1ae7e8c7-94a1-4c67-bcdf-84c307f9e160', 0.000000075000000000, 13333333333.33333200, 0.00000000000430000000, 0.808184143222506, 0.165000000000000, 0.812500000000000, 0.827225130890052, 0.790000000000000, 0.897925000000000, 318.58203125, 37.0000, 0.0000, '{"fpr": [0.0, 0.0, 0.0, 0.005, 0.005, 0.01, 0.01, 0.015, 0.015, 0.025, 0.025, 0.03, 0.03, 0.035, 0.035, 0.04, 0.04, 0.045, 0.045, 0.05, 0.05, 0.055, 0.055, 0.06, 0.06, 0.065, 0.065, 0.07, 0.07, 0.1, 0.1, 0.105, 0.105, 0.125, 0.125, 0.13, 0.13, 0.135, 0.135, 0.14, 0.14, 0.145, 0.145, 0.15, 0.15, 0.16, 0.16, 0.165, 0.165, 0.17, 0.17, 0.185, 0.185, 0.195, 0.195, 0.205, 0.205, 0.21, 0.21, 0.215, 0.215, 0.23, 0.23, 0.24, 0.24, 0.25, 0.25, 0.26, 0.26, 0.27, 0.27, 0.34, 0.34, 0.355, 0.355, 0.38, 0.38, 0.395, 0.395, 0.4, 0.4, 0.41, 0.41, 0.42, 0.42, 0.495, 0.495, 0.515, 0.515, 0.53, 0.53, 0.54, 0.54, 0.585, 0.585, 0.695, 0.695, 0.7, 0.7, 0.745, 0.745, 0.85, 0.85, 1.0], "tpr": [0.0, 0.005, 0.18, 0.18, 0.35, 0.35, 0.425, 0.425, 0.445, 0.445, 0.48, 0.48, 0.485, 0.485, 0.515, 0.515, 0.53, 0.53, 0.575, 0.575, 0.58, 0.58, 0.6, 0.6, 0.64, 0.64, 0.695, 0.695, 0.72, 0.72, 0.725, 0.725, 0.735, 0.735, 0.745, 0.745, 0.755, 0.755, 0.765, 0.765, 0.775, 0.775, 0.78, 0.78, 0.785, 0.785, 0.79, 0.79, 0.805, 0.805, 0.81, 0.81, 0.815, 0.815, 0.82, 0.82, 0.83, 0.83, 0.85, 0.85, 0.855, 0.855, 0.86, 0.86, 0.865, 0.865, 0.885, 0.885, 0.895, 0.895, 0.9, 0.9, 0.905, 0.905, 0.915, 0.915, 0.92, 0.92, 0.925, 0.925, 0.93, 0.93, 0.935, 0.935, 0.94, 0.94, 0.945, 0.945, 0.95, 0.95, 0.955, 0.955, 0.965, 0.965, 0.975, 0.975, 0.98, 0.98, 0.985, 0.985, 0.99, 0.99, 1.0, 1.0]}', '2026-08-24 19:50:47', '2026-08-24 19:50:47', NULL);


--
-- Data for Name: benchmark_runs; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('97aa3683-28ac-4b38-abf4-0d695cb98e31', 1, 3, 'completed', '2026-07-25 17:48:14', '2026-07-25 17:48:16', NULL, '2026-07-25 17:48:10', '2026-07-25 17:48:16', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('cd0e7c5e-3206-4046-af52-aec6a24ff6f3', 1, 3, 'completed', '2026-07-27 17:40:57', '2026-07-27 17:41:03', NULL, '2026-07-27 17:40:49', '2026-07-27 17:41:03', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('c0743109-55bb-4d1f-aec6-8285b188a232', 2, 4, 'completed', '2026-07-25 17:56:59', '2026-07-25 17:57:00', NULL, '2026-07-25 17:56:55', '2026-07-25 17:57:00', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('362050bc-b0df-422a-b204-bca6898dbcef', 2, 3, 'completed', '2026-07-27 17:44:20', '2026-07-27 17:44:25', NULL, '2026-07-27 17:44:12', '2026-07-27 17:44:25', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('1cf7df52-b46d-41db-86d5-62bfc713cb74', 1, 4, 'completed', '2026-07-25 17:59:41', '2026-07-25 17:59:42', NULL, '2026-07-25 17:59:37', '2026-07-25 17:59:42', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee', 1, 3, 'completed', '2026-08-24 19:10:31', '2026-08-24 19:10:39', NULL, '2026-08-24 19:10:20', '2026-08-24 19:10:39', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('2ae91af0-d20b-4e8c-802c-df5377895dbf', 1, 3, 'completed', '2026-07-27 17:47:29', '2026-07-27 17:47:31', NULL, '2026-07-27 17:47:26', '2026-07-27 17:47:31', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('6cc0170d-550c-4083-8771-0874e24bb97c', 1, 2, 'completed', '2026-07-25 18:00:05', '2026-07-25 18:00:07', NULL, '2026-07-25 18:00:03', '2026-07-25 18:00:07', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('5870d533-72ed-41c6-8afb-2cb92590d723', 1, 1, 'completed', '2026-07-25 18:00:24', '2026-07-25 18:00:26', NULL, '2026-07-25 18:00:22', '2026-07-25 18:00:26', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('e726a26c-60fb-4004-a514-1fd7bd465bcd', 1, 3, 'completed', '2026-07-28 20:14:46', '2026-07-28 20:14:48', NULL, '2026-07-28 20:14:42', '2026-07-28 20:14:48', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('d8b6e2e6-c44e-47eb-9071-aeee2a18e635', 1, 2, 'completed', '2026-07-25 18:00:46', '2026-07-25 18:00:48', NULL, '2026-07-25 18:00:44', '2026-07-25 18:00:48', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('d6e866a3-b681-468f-8132-173569dfa6d5', 1, 4, 'completed', '2026-07-28 20:15:22', '2026-07-28 20:15:23', NULL, '2026-07-28 20:15:20', '2026-07-28 20:15:23', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('9cce84b3-7b30-45b5-a61e-9c5052b23abc', 2, 3, 'completed', '2026-07-25 18:01:28', '2026-07-25 18:01:29', NULL, '2026-07-25 18:01:26', '2026-07-25 18:01:29', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('fcebe0c3-2b08-45c5-b50c-df94e9c2fb83', 1, 2, 'completed', '2026-07-28 20:15:53', '2026-07-28 20:15:55', NULL, '2026-07-28 20:15:50', '2026-07-28 20:15:55', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('905a9510-190a-4519-8b61-adf8e59f2608', 2, 4, 'completed', '2026-07-25 18:01:42', '2026-07-25 18:01:43', NULL, '2026-07-25 18:01:40', '2026-07-25 18:01:43', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('b1b62852-98a4-4eaf-bc3a-295584e66fbd', 2, 2, 'completed', '2026-07-25 18:01:55', '2026-07-25 18:01:57', NULL, '2026-07-25 18:01:53', '2026-07-25 18:01:57', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('aa2e74a9-410f-4de9-9472-3037908b8c9f', 1, 1, 'completed', '2026-07-28 20:16:20', '2026-07-28 20:16:22', NULL, '2026-07-28 20:16:17', '2026-07-28 20:16:22', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('306e5b4d-5c5e-4a68-8d25-74415391417d', 2, 1, 'completed', '2026-07-25 18:02:13', '2026-07-25 18:02:15', NULL, '2026-07-25 18:02:10', '2026-07-25 18:02:15', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('4b59be52-61b8-4495-94d4-97826f4acb1e', 2, 3, 'completed', '2026-07-28 20:16:48', '2026-07-28 20:16:50', NULL, '2026-07-28 20:16:46', '2026-07-28 20:16:50', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('cdcf6f60-04b4-41a1-b7fa-393622b4a7dd', 1, 3, 'completed', '2026-07-25 18:07:59', '2026-07-25 18:08:00', NULL, '2026-07-25 18:07:56', '2026-07-25 18:08:00', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('995d2361-8ee7-42ac-a32f-166f52943b04', 1, 3, 'completed', '2026-07-28 20:17:52', '2026-07-28 20:17:53', NULL, '2026-07-28 20:17:50', '2026-07-28 20:17:53', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('316e13ae-fba0-434a-9b3d-1b74f9199ea0', 1, 4, 'completed', '2026-07-25 18:08:10', '2026-07-25 18:08:10', NULL, '2026-07-25 18:08:07', '2026-07-25 18:08:10', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('8616be07-4800-4482-a2c5-a8819f8c6681', 1, 5, 'failed', '2026-08-24 19:38:06', '2026-08-24 19:38:06', 'Traceback (most recent call last):
  File "/var/www/html/python/cli/run_benchmark.py", line 13, in <module>
    from backends import cpu_baseline, gpu_baseline, ibm_nscs, lava_loihi, mnsim_imc
  File "/var/www/html/python/backends/cpu_baseline.py", line 3, in <module>
    from sklearn.neural_network import MLPClassifier
ModuleNotFoundError: No module named ''sklearn''', '2026-08-24 19:38:06', '2026-08-24 19:38:06', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('a2951678-014d-45af-be11-c0eef26559ee', 1, 2, 'completed', '2026-07-25 18:08:27', '2026-07-25 18:08:29', NULL, '2026-07-25 18:08:24', '2026-07-25 18:08:29', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('cc28f034-e665-4c3d-bd56-9e3eed49e7cf', 2, 3, 'completed', '2026-07-28 20:18:59', '2026-07-28 20:19:01', NULL, '2026-07-28 20:18:57', '2026-07-28 20:19:01', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('4d3503b4-f230-408a-ab02-5234d395441d', 1, 1, 'completed', '2026-07-25 18:08:46', '2026-07-25 18:08:48', NULL, '2026-07-25 18:08:44', '2026-07-25 18:08:48', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('a205385e-ad6f-4163-aa9a-a7b79286170c', 2, 4, 'completed', '2026-07-28 20:19:41', '2026-07-28 20:19:41', NULL, '2026-07-28 20:19:38', '2026-07-28 20:19:41', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('388f6a0b-3fa0-47e3-b982-53f8e2e906e3', 2, 3, 'completed', '2026-07-25 18:09:16', '2026-07-25 18:09:18', NULL, '2026-07-25 18:09:14', '2026-07-25 18:09:18', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('6db240eb-5748-4f6c-a7dd-b0f89df04649', 2, 2, 'completed', '2026-07-28 20:20:18', '2026-07-28 20:20:20', NULL, '2026-07-28 20:20:16', '2026-07-28 20:20:20', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('33c170cf-9857-4fbc-8799-2c20d97b7fba', 2, 4, 'completed', '2026-07-25 18:09:29', '2026-07-25 18:09:29', NULL, '2026-07-25 18:09:26', '2026-07-25 18:09:29', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('dcecfd50-0850-42e8-80f2-0119118c6edc', 1, 5, 'failed', '2026-08-24 19:38:56', '2026-08-24 19:38:56', 'Traceback (most recent call last):
  File "/var/www/html/python/cli/run_benchmark.py", line 13, in <module>
    from backends import cpu_baseline, gpu_baseline, ibm_nscs, lava_loihi, mnsim_imc
  File "/var/www/html/python/backends/cpu_baseline.py", line 3, in <module>
    from sklearn.neural_network import MLPClassifier
ModuleNotFoundError: No module named ''sklearn''', '2026-08-24 19:38:56', '2026-08-24 19:38:56', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('072b0f0d-1b56-44c5-b8ed-19421c60f761', 2, 2, 'completed', '2026-07-25 18:09:41', '2026-07-25 18:09:42', NULL, '2026-07-25 18:09:38', '2026-07-25 18:09:42', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('f5854c19-29b2-4cae-88d2-b55de3d90b47', 2, 1, 'completed', '2026-07-28 20:20:51', '2026-07-28 20:20:53', NULL, '2026-07-28 20:20:48', '2026-07-28 20:20:53', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('985c9392-f957-4b08-a6ed-2058e6738d49', 2, 1, 'completed', '2026-07-25 18:09:53', '2026-07-25 18:09:55', NULL, '2026-07-25 18:09:51', '2026-07-25 18:09:55', NULL);
INSERT INTO public.benchmark_runs (id, dataset_id, architecture_id, status, started_at, finished_at, error_message, created_at, updated_at, deleted_at) VALUES ('1ae7e8c7-94a1-4c67-bcdf-84c307f9e160', 1, 5, 'completed', '2026-08-24 19:50:45', '2026-08-24 19:50:47', NULL, '2026-08-24 19:50:43', '2026-08-24 19:50:47', NULL);


--
-- Data for Name: datasets; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.datasets (id, slug, name, created_at, updated_at, deleted_at) VALUES (1, 'cicids', 'CICIDS', '2026-07-25 17:23:37', '2026-07-25 17:23:37', NULL);
INSERT INTO public.datasets (id, slug, name, created_at, updated_at, deleted_at) VALUES (2, 'unsw_nb15', 'UNSW-NB15', '2026-07-25 17:23:37', '2026-07-25 17:23:37', NULL);


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.migrations (id, migration, batch) VALUES (1, '2024_01_01_000001_create_datasets_table', 1);
INSERT INTO public.migrations (id, migration, batch) VALUES (2, '2024_01_01_000002_create_architectures_table', 1);
INSERT INTO public.migrations (id, migration, batch) VALUES (3, '2024_01_01_000003_create_benchmark_runs_table', 1);
INSERT INTO public.migrations (id, migration, batch) VALUES (4, '2024_01_01_000004_create_benchmark_metrics_table', 1);
INSERT INTO public.migrations (id, migration, batch) VALUES (5, '2026_07_25_000001_widen_energy_joules_per_op_column', 2);
INSERT INTO public.migrations (id, migration, batch) VALUES (6, '2026_07_25_000002_widen_benchmark_metric_numeric_columns', 3);
INSERT INTO public.migrations (id, migration, batch) VALUES (7, '2026_07_25_000003_convert_benchmark_metrics_to_decimal', 4);
INSERT INTO public.migrations (id, migration, batch) VALUES (8, '2026_08_24_000001_add_extended_metrics_and_keep_legacy', 5);
INSERT INTO public.migrations (id, migration, batch) VALUES (9, '2026_08_24_000002_move_benchmark_metrics_timestamps_last', 6);
INSERT INTO public.migrations (id, migration, batch) VALUES (10, '2026_08_24_000003_add_soft_deletes_to_all_tables', 7);


--
-- Name: architectures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.architectures_id_seq', 5, true);


--
-- Name: benchmark_metrics_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.benchmark_metrics_id_seq', 34, true);


--
-- Name: datasets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.datasets_id_seq', 2, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 10, true);


--
-- Name: architectures architectures_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.architectures
    ADD CONSTRAINT architectures_pkey PRIMARY KEY (id);


--
-- Name: architectures architectures_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.architectures
    ADD CONSTRAINT architectures_slug_unique UNIQUE (slug);


--
-- Name: benchmark_metrics benchmark_metrics_benchmark_run_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.benchmark_metrics
    ADD CONSTRAINT benchmark_metrics_benchmark_run_id_unique UNIQUE (benchmark_run_id);


--
-- Name: benchmark_metrics benchmark_metrics_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.benchmark_metrics
    ADD CONSTRAINT benchmark_metrics_pkey PRIMARY KEY (id);


--
-- Name: benchmark_runs benchmark_runs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.benchmark_runs
    ADD CONSTRAINT benchmark_runs_pkey PRIMARY KEY (id);


--
-- Name: datasets datasets_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datasets
    ADD CONSTRAINT datasets_pkey PRIMARY KEY (id);


--
-- Name: datasets datasets_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.datasets
    ADD CONSTRAINT datasets_slug_unique UNIQUE (slug);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: benchmark_runs_status_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX benchmark_runs_status_created_at_index ON public.benchmark_runs USING btree (status, created_at);


--
-- Name: benchmark_metrics benchmark_metrics_benchmark_run_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.benchmark_metrics
    ADD CONSTRAINT benchmark_metrics_benchmark_run_id_foreign FOREIGN KEY (benchmark_run_id) REFERENCES public.benchmark_runs(id) ON DELETE CASCADE;


--
-- Name: benchmark_runs benchmark_runs_architecture_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.benchmark_runs
    ADD CONSTRAINT benchmark_runs_architecture_id_foreign FOREIGN KEY (architecture_id) REFERENCES public.architectures(id) ON DELETE CASCADE;


--
-- Name: benchmark_runs benchmark_runs_dataset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.benchmark_runs
    ADD CONSTRAINT benchmark_runs_dataset_id_foreign FOREIGN KEY (dataset_id) REFERENCES public.datasets(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

