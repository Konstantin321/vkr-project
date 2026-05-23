--
-- PostgreSQL database dump
--

\restrict ccjnQG9JjXhDNhz4oK0MUT4W0QB5HeDP7goRcChqEvcaZRUBH5nZehmGdwj890U

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

-- Started on 2026-05-23 17:33:45

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 238 (class 1259 OID 16777)
-- Name: answers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.answers (
    id integer NOT NULL,
    attempt_id integer NOT NULL,
    task_id integer NOT NULL,
    answer_text text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    score numeric(8,2) DEFAULT 0
);


ALTER TABLE public.answers OWNER TO postgres;

--
-- TOC entry 237 (class 1259 OID 16776)
-- Name: answers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.answers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.answers_id_seq OWNER TO postgres;

--
-- TOC entry 5169 (class 0 OID 0)
-- Dependencies: 237
-- Name: answers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.answers_id_seq OWNED BY public.answers.id;


--
-- TOC entry 234 (class 1259 OID 16736)
-- Name: attempts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attempts (
    id integer NOT NULL,
    task_set_id integer NOT NULL,
    student_id integer NOT NULL,
    started_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    finished_at timestamp without time zone,
    status character varying(50) NOT NULL
);


ALTER TABLE public.attempts OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 16735)
-- Name: attempts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attempts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attempts_id_seq OWNER TO postgres;

--
-- TOC entry 5170 (class 0 OID 0)
-- Dependencies: 233
-- Name: attempts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attempts_id_seq OWNED BY public.attempts.id;


--
-- TOC entry 222 (class 1259 OID 16602)
-- Name: disciplines; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.disciplines (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text
);


ALTER TABLE public.disciplines OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 16601)
-- Name: disciplines_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.disciplines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.disciplines_id_seq OWNER TO postgres;

--
-- TOC entry 5171 (class 0 OID 0)
-- Dependencies: 221
-- Name: disciplines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.disciplines_id_seq OWNED BY public.disciplines.id;


--
-- TOC entry 226 (class 1259 OID 16626)
-- Name: folders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.folders (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    parent_folder_id bigint
);


ALTER TABLE public.folders OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 16625)
-- Name: folders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.folders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.folders_id_seq OWNER TO postgres;

--
-- TOC entry 5172 (class 0 OID 0)
-- Dependencies: 225
-- Name: folders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.folders_id_seq OWNED BY public.folders.id;


--
-- TOC entry 236 (class 1259 OID 16758)
-- Name: results; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.results (
    id integer NOT NULL,
    attempt_id integer NOT NULL,
    total_score numeric(8,2) DEFAULT 0 NOT NULL,
    grade character varying(20),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    score_breakdown text
);


ALTER TABLE public.results OWNER TO postgres;

--
-- TOC entry 235 (class 1259 OID 16757)
-- Name: results_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.results_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.results_id_seq OWNER TO postgres;

--
-- TOC entry 5173 (class 0 OID 0)
-- Dependencies: 235
-- Name: results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.results_id_seq OWNED BY public.results.id;


--
-- TOC entry 242 (class 1259 OID 16827)
-- Name: task_options; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.task_options (
    id bigint NOT NULL,
    task_id bigint NOT NULL,
    option_text text NOT NULL,
    is_correct boolean DEFAULT false NOT NULL,
    sort_order integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.task_options OWNER TO postgres;

--
-- TOC entry 241 (class 1259 OID 16826)
-- Name: task_options_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.task_options_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.task_options_id_seq OWNER TO postgres;

--
-- TOC entry 5174 (class 0 OID 0)
-- Dependencies: 241
-- Name: task_options_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.task_options_id_seq OWNED BY public.task_options.id;


--
-- TOC entry 232 (class 1259 OID 16699)
-- Name: task_set_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.task_set_items (
    id bigint NOT NULL,
    task_set_id bigint NOT NULL,
    task_id bigint NOT NULL,
    order_number integer NOT NULL,
    max_score numeric(6,2) NOT NULL,
    CONSTRAINT chk_task_set_items_max_score CHECK ((max_score >= (0)::numeric)),
    CONSTRAINT chk_task_set_items_order_number CHECK ((order_number > 0)),
    CONSTRAINT task_set_items_max_score_check CHECK ((max_score >= (0)::numeric))
);


ALTER TABLE public.task_set_items OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 16698)
-- Name: task_set_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.task_set_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.task_set_items_id_seq OWNER TO postgres;

--
-- TOC entry 5175 (class 0 OID 0)
-- Dependencies: 231
-- Name: task_set_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.task_set_items_id_seq OWNED BY public.task_set_items.id;


--
-- TOC entry 230 (class 1259 OID 16679)
-- Name: task_sets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.task_sets (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    execution_time_minutes integer NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_by bigint NOT NULL,
    CONSTRAINT task_sets_execution_time_minutes_check CHECK ((execution_time_minutes > 0))
);


ALTER TABLE public.task_sets OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 16678)
-- Name: task_sets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.task_sets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.task_sets_id_seq OWNER TO postgres;

--
-- TOC entry 5176 (class 0 OID 0)
-- Dependencies: 229
-- Name: task_sets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.task_sets_id_seq OWNED BY public.task_sets.id;


--
-- TOC entry 224 (class 1259 OID 16613)
-- Name: task_types; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.task_types (
    id bigint NOT NULL,
    name character varying(100) NOT NULL,
    description text
);


ALTER TABLE public.task_types OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 16612)
-- Name: task_types_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.task_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.task_types_id_seq OWNER TO postgres;

--
-- TOC entry 5177 (class 0 OID 0)
-- Dependencies: 223
-- Name: task_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.task_types_id_seq OWNED BY public.task_types.id;


--
-- TOC entry 228 (class 1259 OID 16640)
-- Name: tasks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tasks (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    task_text text NOT NULL,
    difficulty character varying(50),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    purpose text,
    reference_answer text,
    task_type_id bigint NOT NULL,
    discipline_id bigint NOT NULL,
    folder_id bigint,
    author_id bigint NOT NULL
);


ALTER TABLE public.tasks OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 16639)
-- Name: tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tasks_id_seq OWNER TO postgres;

--
-- TOC entry 5178 (class 0 OID 0)
-- Dependencies: 227
-- Name: tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tasks_id_seq OWNED BY public.tasks.id;


--
-- TOC entry 240 (class 1259 OID 16801)
-- Name: teacher_comments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.teacher_comments (
    id integer NOT NULL,
    answer_id integer NOT NULL,
    teacher_id integer NOT NULL,
    comment_text text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.teacher_comments OWNER TO postgres;

--
-- TOC entry 239 (class 1259 OID 16800)
-- Name: teacher_comments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.teacher_comments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.teacher_comments_id_seq OWNER TO postgres;

--
-- TOC entry 5179 (class 0 OID 0)
-- Dependencies: 239
-- Name: teacher_comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.teacher_comments_id_seq OWNED BY public.teacher_comments.id;


--
-- TOC entry 220 (class 1259 OID 16583)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    full_name character varying(255) NOT NULL,
    login character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    role character varying(50) NOT NULL,
    password_hash character varying(255),
    CONSTRAINT users_role_check CHECK (((role)::text = ANY ((ARRAY['teacher'::character varying, 'admin'::character varying, 'student'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 16582)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 5180 (class 0 OID 0)
-- Dependencies: 219
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 4926 (class 2604 OID 16780)
-- Name: answers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.answers ALTER COLUMN id SET DEFAULT nextval('public.answers_id_seq'::regclass);


--
-- TOC entry 4921 (class 2604 OID 16739)
-- Name: attempts id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attempts ALTER COLUMN id SET DEFAULT nextval('public.attempts_id_seq'::regclass);


--
-- TOC entry 4912 (class 2604 OID 16605)
-- Name: disciplines id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disciplines ALTER COLUMN id SET DEFAULT nextval('public.disciplines_id_seq'::regclass);


--
-- TOC entry 4914 (class 2604 OID 16629)
-- Name: folders id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.folders ALTER COLUMN id SET DEFAULT nextval('public.folders_id_seq'::regclass);


--
-- TOC entry 4923 (class 2604 OID 16761)
-- Name: results id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.results ALTER COLUMN id SET DEFAULT nextval('public.results_id_seq'::regclass);


--
-- TOC entry 4931 (class 2604 OID 16830)
-- Name: task_options id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_options ALTER COLUMN id SET DEFAULT nextval('public.task_options_id_seq'::regclass);


--
-- TOC entry 4920 (class 2604 OID 16702)
-- Name: task_set_items id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_set_items ALTER COLUMN id SET DEFAULT nextval('public.task_set_items_id_seq'::regclass);


--
-- TOC entry 4918 (class 2604 OID 16682)
-- Name: task_sets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_sets ALTER COLUMN id SET DEFAULT nextval('public.task_sets_id_seq'::regclass);


--
-- TOC entry 4913 (class 2604 OID 16616)
-- Name: task_types id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_types ALTER COLUMN id SET DEFAULT nextval('public.task_types_id_seq'::regclass);


--
-- TOC entry 4915 (class 2604 OID 16643)
-- Name: tasks id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tasks ALTER COLUMN id SET DEFAULT nextval('public.tasks_id_seq'::regclass);


--
-- TOC entry 4929 (class 2604 OID 16804)
-- Name: teacher_comments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teacher_comments ALTER COLUMN id SET DEFAULT nextval('public.teacher_comments_id_seq'::regclass);


--
-- TOC entry 4911 (class 2604 OID 16586)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 5159 (class 0 OID 16777)
-- Dependencies: 238
-- Data for Name: answers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.answers (id, attempt_id, task_id, answer_text, created_at, score) FROM stdin;
26	26	8		2026-05-23 15:41:16.401251	4.00
27	26	5		2026-05-23 15:41:16.402347	433.00
\.


--
-- TOC entry 5155 (class 0 OID 16736)
-- Dependencies: 234
-- Data for Name: attempts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attempts (id, task_set_id, student_id, started_at, finished_at, status) FROM stdin;
13	3	1	2026-04-15 16:57:03.528341	2026-04-15 16:57:07.421187	completed
14	3	1	2026-04-15 17:10:33.949293	\N	started
19	3	1	2026-05-19 11:44:32.101322	\N	started
24	3	1	2026-05-23 15:39:47.430227	\N	started
25	4	1	2026-05-23 15:40:30.859267	\N	started
26	4	1	2026-05-23 15:41:13.070636	2026-05-23 15:41:16.402739	completed
27	4	1	2026-05-23 17:18:47.959747	\N	started
\.


--
-- TOC entry 5143 (class 0 OID 16602)
-- Dependencies: 222
-- Data for Name: disciplines; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.disciplines (id, name, description) FROM stdin;
1	САПР	Системы автоматизированного проектирования
2	Программирование	Основы программирования
\.


--
-- TOC entry 5147 (class 0 OID 16626)
-- Dependencies: 226
-- Data for Name: folders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.folders (id, name, parent_folder_id) FROM stdin;
1	Основная папка	\N
2	Контрольные работы	1
\.


--
-- TOC entry 5157 (class 0 OID 16758)
-- Dependencies: 236
-- Data for Name: results; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.results (id, attempt_id, total_score, grade, created_at, score_breakdown) FROM stdin;
6	13	0.00	Не проверено	2026-04-15 16:57:07.423614	\N
11	26	437.00	Проверено	2026-05-23 15:41:16.40476	Задание 1: 4.00 из 1.00; Задание 2: 433.00 из 1.00
\.


--
-- TOC entry 5163 (class 0 OID 16827)
-- Dependencies: 242
-- Data for Name: task_options; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.task_options (id, task_id, option_text, is_correct, sort_order) FROM stdin;
\.


--
-- TOC entry 5153 (class 0 OID 16699)
-- Dependencies: 232
-- Data for Name: task_set_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.task_set_items (id, task_set_id, task_id, order_number, max_score) FROM stdin;
26	4	8	1	1.00
27	4	5	2	1.00
\.


--
-- TOC entry 5151 (class 0 OID 16679)
-- Dependencies: 230
-- Data for Name: task_sets; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.task_sets (id, name, description, execution_time_minutes, created_at, created_by) FROM stdin;
3	test3	test3	62	2026-04-15 07:24:00.862803	1
4	Тестовый набор 2	ыв	1	2026-05-23 15:40:07.703008	1
\.


--
-- TOC entry 5145 (class 0 OID 16613)
-- Dependencies: 224
-- Data for Name: task_types; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.task_types (id, name, description) FROM stdin;
1	Открытый ответ	Задание с открытым текстовым ответом
2	Один вариант	Выбор одного правильного варианта
3	Несколько вариантов	Выбор нескольких правильных вариантов
\.


--
-- TOC entry 5149 (class 0 OID 16640)
-- Dependencies: 228
-- Data for Name: tasks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tasks (id, title, task_text, difficulty, created_at, is_active, purpose, reference_answer, task_type_id, discipline_id, folder_id, author_id) FROM stdin;
5	Триггер D-типа	Объяснить принцип работы D-триггера.	легкая	2026-04-15 04:13:40.942423	t	Тренировочная работа	Запоминает значение на фронте clk	2	1	\N	1
8	Регистры	Что такое регистр и зачем он нужен?	легкая	2026-04-15 04:13:40.942423	t	Теоретический вопрос	Устройство хранения данных	2	1	\N	1
\.


--
-- TOC entry 5161 (class 0 OID 16801)
-- Dependencies: 240
-- Data for Name: teacher_comments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.teacher_comments (id, answer_id, teacher_id, comment_text, created_at) FROM stdin;
16	26	1	34	2026-05-23 17:19:06.501925
17	27	1	4r34	2026-05-23 17:19:10.455397
\.


--
-- TOC entry 5141 (class 0 OID 16583)
-- Dependencies: 220
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, full_name, login, email, role, password_hash) FROM stdin;
1	Иванов Иван Иванович	teacher	teacher@example.local	teacher	$2y$10$JVdYca7QqAomiI8DJVS7TuZe57QIXxrRKusALlehOyx/0408sCbpS
2	Pavel	Pavel	pavel@example.local	admin	$2y$10$Xudxa71w3QGVQVjRNHtlDOqPXWaSM3Ml4plHY5UEbhAZNzNeVOEde
\.


--
-- TOC entry 5181 (class 0 OID 0)
-- Dependencies: 237
-- Name: answers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.answers_id_seq', 27, true);


--
-- TOC entry 5182 (class 0 OID 0)
-- Dependencies: 233
-- Name: attempts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attempts_id_seq', 27, true);


--
-- TOC entry 5183 (class 0 OID 0)
-- Dependencies: 221
-- Name: disciplines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.disciplines_id_seq', 2, true);


--
-- TOC entry 5184 (class 0 OID 0)
-- Dependencies: 225
-- Name: folders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.folders_id_seq', 2, true);


--
-- TOC entry 5185 (class 0 OID 0)
-- Dependencies: 235
-- Name: results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.results_id_seq', 11, true);


--
-- TOC entry 5186 (class 0 OID 0)
-- Dependencies: 241
-- Name: task_options_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.task_options_id_seq', 1, false);


--
-- TOC entry 5187 (class 0 OID 0)
-- Dependencies: 231
-- Name: task_set_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.task_set_items_id_seq', 27, true);


--
-- TOC entry 5188 (class 0 OID 0)
-- Dependencies: 229
-- Name: task_sets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.task_sets_id_seq', 4, true);


--
-- TOC entry 5189 (class 0 OID 0)
-- Dependencies: 223
-- Name: task_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.task_types_id_seq', 3, true);


--
-- TOC entry 5190 (class 0 OID 0)
-- Dependencies: 227
-- Name: tasks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tasks_id_seq', 18, true);


--
-- TOC entry 5191 (class 0 OID 0)
-- Dependencies: 239
-- Name: teacher_comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.teacher_comments_id_seq', 17, true);


--
-- TOC entry 5192 (class 0 OID 0)
-- Dependencies: 219
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- TOC entry 4972 (class 2606 OID 16788)
-- Name: answers answers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT answers_pkey PRIMARY KEY (id);


--
-- TOC entry 4966 (class 2606 OID 16746)
-- Name: attempts attempts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attempts
    ADD CONSTRAINT attempts_pkey PRIMARY KEY (id);


--
-- TOC entry 4948 (class 2606 OID 16611)
-- Name: disciplines disciplines_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.disciplines
    ADD CONSTRAINT disciplines_pkey PRIMARY KEY (id);


--
-- TOC entry 4954 (class 2606 OID 16633)
-- Name: folders folders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.folders
    ADD CONSTRAINT folders_pkey PRIMARY KEY (id);


--
-- TOC entry 4968 (class 2606 OID 16770)
-- Name: results results_attempt_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.results
    ADD CONSTRAINT results_attempt_id_key UNIQUE (attempt_id);


--
-- TOC entry 4970 (class 2606 OID 16768)
-- Name: results results_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.results
    ADD CONSTRAINT results_pkey PRIMARY KEY (id);


--
-- TOC entry 4976 (class 2606 OID 16841)
-- Name: task_options task_options_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_options
    ADD CONSTRAINT task_options_pkey PRIMARY KEY (id);


--
-- TOC entry 4960 (class 2606 OID 16710)
-- Name: task_set_items task_set_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_set_items
    ADD CONSTRAINT task_set_items_pkey PRIMARY KEY (id);


--
-- TOC entry 4958 (class 2606 OID 16692)
-- Name: task_sets task_sets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_sets
    ADD CONSTRAINT task_sets_pkey PRIMARY KEY (id);


--
-- TOC entry 4950 (class 2606 OID 16624)
-- Name: task_types task_types_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_types
    ADD CONSTRAINT task_types_name_key UNIQUE (name);


--
-- TOC entry 4952 (class 2606 OID 16622)
-- Name: task_types task_types_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_types
    ADD CONSTRAINT task_types_pkey PRIMARY KEY (id);


--
-- TOC entry 4956 (class 2606 OID 16657)
-- Name: tasks tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_pkey PRIMARY KEY (id);


--
-- TOC entry 4974 (class 2606 OID 16813)
-- Name: teacher_comments teacher_comments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teacher_comments
    ADD CONSTRAINT teacher_comments_pkey PRIMARY KEY (id);


--
-- TOC entry 4962 (class 2606 OID 16734)
-- Name: task_set_items uq_task_set_items_task_set_order; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_set_items
    ADD CONSTRAINT uq_task_set_items_task_set_order UNIQUE (task_set_id, order_number);


--
-- TOC entry 4964 (class 2606 OID 16732)
-- Name: task_set_items uq_task_set_items_task_set_task; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_set_items
    ADD CONSTRAINT uq_task_set_items_task_set_task UNIQUE (task_set_id, task_id);


--
-- TOC entry 4940 (class 2606 OID 16600)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 4943 (class 2606 OID 16598)
-- Name: users users_login_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_login_key UNIQUE (login);


--
-- TOC entry 4946 (class 2606 OID 16596)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 4941 (class 1259 OID 16848)
-- Name: users_email_unique; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX users_email_unique ON public.users USING btree (email) WHERE (email IS NOT NULL);


--
-- TOC entry 4944 (class 1259 OID 16847)
-- Name: users_login_unique; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX users_login_unique ON public.users USING btree (login) WHERE (login IS NOT NULL);


--
-- TOC entry 4988 (class 2606 OID 16789)
-- Name: answers fk_answers_attempt; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT fk_answers_attempt FOREIGN KEY (attempt_id) REFERENCES public.attempts(id) ON DELETE CASCADE;


--
-- TOC entry 4989 (class 2606 OID 16794)
-- Name: answers fk_answers_task; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT fk_answers_task FOREIGN KEY (task_id) REFERENCES public.tasks(id) ON DELETE CASCADE;


--
-- TOC entry 4985 (class 2606 OID 16747)
-- Name: attempts fk_attempts_task_set; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attempts
    ADD CONSTRAINT fk_attempts_task_set FOREIGN KEY (task_set_id) REFERENCES public.task_sets(id) ON DELETE CASCADE;


--
-- TOC entry 4986 (class 2606 OID 16752)
-- Name: attempts fk_attempts_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attempts
    ADD CONSTRAINT fk_attempts_user FOREIGN KEY (student_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 4987 (class 2606 OID 16771)
-- Name: results fk_results_attempt; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.results
    ADD CONSTRAINT fk_results_attempt FOREIGN KEY (attempt_id) REFERENCES public.attempts(id) ON DELETE CASCADE;


--
-- TOC entry 4983 (class 2606 OID 16716)
-- Name: task_set_items fk_task_set_items_task; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_set_items
    ADD CONSTRAINT fk_task_set_items_task FOREIGN KEY (task_id) REFERENCES public.tasks(id) ON DELETE CASCADE;


--
-- TOC entry 4984 (class 2606 OID 16711)
-- Name: task_set_items fk_task_set_items_task_set; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_set_items
    ADD CONSTRAINT fk_task_set_items_task_set FOREIGN KEY (task_set_id) REFERENCES public.task_sets(id) ON DELETE CASCADE;


--
-- TOC entry 4982 (class 2606 OID 16693)
-- Name: task_sets fk_task_sets_created_by; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_sets
    ADD CONSTRAINT fk_task_sets_created_by FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- TOC entry 4990 (class 2606 OID 16814)
-- Name: teacher_comments fk_teacher_comments_answer; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teacher_comments
    ADD CONSTRAINT fk_teacher_comments_answer FOREIGN KEY (answer_id) REFERENCES public.answers(id) ON DELETE CASCADE;


--
-- TOC entry 4991 (class 2606 OID 16819)
-- Name: teacher_comments fk_teacher_comments_teacher; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.teacher_comments
    ADD CONSTRAINT fk_teacher_comments_teacher FOREIGN KEY (teacher_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 4977 (class 2606 OID 16634)
-- Name: folders folders_parent_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.folders
    ADD CONSTRAINT folders_parent_folder_id_fkey FOREIGN KEY (parent_folder_id) REFERENCES public.folders(id) ON DELETE SET NULL;


--
-- TOC entry 4992 (class 2606 OID 16842)
-- Name: task_options task_options_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.task_options
    ADD CONSTRAINT task_options_task_id_fkey FOREIGN KEY (task_id) REFERENCES public.tasks(id) ON DELETE CASCADE;


--
-- TOC entry 4978 (class 2606 OID 16673)
-- Name: tasks tasks_author_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_author_id_fkey FOREIGN KEY (author_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- TOC entry 4979 (class 2606 OID 16663)
-- Name: tasks tasks_discipline_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_discipline_id_fkey FOREIGN KEY (discipline_id) REFERENCES public.disciplines(id) ON DELETE RESTRICT;


--
-- TOC entry 4980 (class 2606 OID 16668)
-- Name: tasks tasks_folder_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_folder_id_fkey FOREIGN KEY (folder_id) REFERENCES public.folders(id) ON DELETE SET NULL;


--
-- TOC entry 4981 (class 2606 OID 16658)
-- Name: tasks tasks_task_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_task_type_id_fkey FOREIGN KEY (task_type_id) REFERENCES public.task_types(id) ON DELETE RESTRICT;


-- Completed on 2026-05-23 17:33:45

--
-- PostgreSQL database dump complete
--

\unrestrict ccjnQG9JjXhDNhz4oK0MUT4W0QB5HeDP7goRcChqEvcaZRUBH5nZehmGdwj890U

