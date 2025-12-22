--
-- PostgreSQL database dump
--

-- Dumped from database version 17.4
-- Dumped by pg_dump version 17.4

-- Started on 2025-12-11 01:40:06

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
-- TOC entry 217 (class 1259 OID 19592)
-- Name: baithi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.baithi (
    mabaithi character varying(50) NOT NULL,
    madethi character varying(50) NOT NULL,
    madangkycanhan character varying(50),
    madangkydoi character varying(50),
    loaidangky character varying(20),
    filebaithi character varying(500),
    thoigiannop timestamp with time zone,
    trangthai character varying(50) DEFAULT 'Submitted'::character varying,
    CONSTRAINT baithi_loaidangky_check CHECK (((loaidangky)::text = ANY (ARRAY[('CaNhan'::character varying)::text, ('DoiNhom'::character varying)::text]))),
    CONSTRAINT check_dangky CHECK (((((loaidangky)::text = 'CaNhan'::text) AND (madangkycanhan IS NOT NULL) AND (madangkydoi IS NULL)) OR (((loaidangky)::text = 'DoiNhom'::text) AND (madangkydoi IS NOT NULL) AND (madangkycanhan IS NULL))))
);



--
-- TOC entry 218 (class 1259 OID 19600)
-- Name: ban; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ban (
    maban character varying(50) NOT NULL,
    tenban character varying(200) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    mota text
);


--
-- TOC entry 219 (class 1259 OID 19605)
-- Name: bomon; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.bomon (
    mabomon character varying(50) NOT NULL,
    tenbomon character varying(200) NOT NULL,
    matruongbomon character varying(50),
    mota text
);



--
-- TOC entry 220 (class 1259 OID 19610)
-- Name: chiphi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.chiphi (
    machiphi character varying(50) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    tenkhoanchi character varying(300) NOT NULL,
    dutruchiphi numeric(15,2),
    thuctechi numeric(15,2),
    ngaychi date,
    nguoiduyet character varying(50),
    trangthai character varying(50) DEFAULT 'Pending'::character varying,
    chungtu character varying(500),
    ghichu text,
    nguoiyeucau character varying(50),
    ngayyeucau date,
    ngayduyet date,
    magangiai character varying(50)
);



--
-- TOC entry 248 (class 1259 OID 20172)
-- Name: cocaugiaithuong; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cocaugiaithuong (
    macocau character varying(50) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    tengiai character varying(100) NOT NULL,
    soluong integer DEFAULT 1 NOT NULL,
    tienthuong numeric(15,2) DEFAULT 0,
    giaykhen boolean DEFAULT true,
    chophepdonghang boolean DEFAULT true,
    ghichudonghang text,
    ghichu text,
    trangthai character varying(20) DEFAULT 'Active'::character varying,
    ngaytao timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT cocaugiaithuong_trangthai_check CHECK (((trangthai)::text = ANY ((ARRAY['Active'::character varying, 'Inactive'::character varying])::text[])))
);


--
-- TOC entry 221 (class 1259 OID 19616)
-- Name: congviec; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.congviec (
    macongviec character varying(50) NOT NULL,
    tencongviec character varying(300) NOT NULL,
    maban character varying(50),
    macuocthi character varying(50) NOT NULL,
    mota text,
    thoigianbatdau timestamp with time zone,
    thoigianketthuc timestamp with time zone,
    trangthai character varying(50) DEFAULT 'Pending'::character varying
);



--
-- TOC entry 222 (class 1259 OID 19622)
-- Name: cuocthi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cuocthi (
    macuocthi character varying(50) NOT NULL,
    tencuocthi character varying(300) NOT NULL,
    loaicuocthi character varying(50),
    mota text,
    mucdich text,
    doituongthamgia character varying(200),
    thoigianbatdau timestamp with time zone,
    thoigianketthuc timestamp with time zone,
    diadiem character varying(300),
    soluongthanhvien integer,
    hinhthucthamgia character varying(50),
    trangthai character varying(50) DEFAULT 'Draft'::character varying,
    dutrukinhphi numeric(15,2),
    chiphithucte numeric(15,2),
    mabomon character varying(50),
    ngaytao timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    ngaycapnhat timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    makehoach character varying(50),
    CONSTRAINT cuocthi_hinhthucthamgia_check CHECK (((hinhthucthamgia)::text = ANY (ARRAY[('CaNhan'::character varying)::text, ('DoiNhom'::character varying)::text, ('CaHai'::character varying)::text]))),
    CONSTRAINT cuocthi_loaicuocthi_check CHECK (((loaicuocthi)::text = ANY (ARRAY[('CuocThi'::character varying)::text, ('Seminar'::character varying)::text, ('HoiThao'::character varying)::text]))),
    CONSTRAINT cuocthi_trangthai_check CHECK (((trangthai)::text = ANY (ARRAY[('Draft'::character varying)::text, ('Approved'::character varying)::text, ('InProgress'::character varying)::text, ('Completed'::character varying)::text, ('Cancelled'::character varying)::text])))
);


--
-- TOC entry 223 (class 1259 OID 19633)
-- Name: dangkycanhan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dangkycanhan (
    madangkycanhan character varying(50) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    masinhvien character varying(50) NOT NULL,
    ngaydangky timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    trangthai character varying(50) DEFAULT 'Registered'::character varying,
    ghichu text,
    CONSTRAINT dangkycanhan_trangthai_check CHECK (((trangthai)::text = ANY (ARRAY[('Registered'::character varying)::text, ('Confirmed'::character varying)::text, ('Cancelled'::character varying)::text, ('Completed'::character varying)::text])))
);



--
-- TOC entry 224 (class 1259 OID 19641)
-- Name: dangkydoithi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dangkydoithi (
    madangkydoi character varying(50) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    madoithi character varying(50) NOT NULL,
    ngaydangky timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    trangthai character varying(50) DEFAULT 'Registered'::character varying,
    ghichu text,
    CONSTRAINT dangkydoithi_trangthai_check CHECK (((trangthai)::text = ANY (ARRAY[('Registered'::character varying)::text, ('Confirmed'::character varying)::text, ('Cancelled'::character varying)::text, ('Completed'::character varying)::text])))
);


--
-- TOC entry 225 (class 1259 OID 19649)
-- Name: dangkyhoatdong; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dangkyhoatdong (
    madangkyhoatdong character varying(50) NOT NULL,
    mahoatdong character varying(50) NOT NULL,
    masinhvien character varying(50) NOT NULL,
    ngaydangky timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    trangthai character varying(50) DEFAULT 'Registered'::character varying,
    diemdanhqr boolean DEFAULT false,
    thoigiandiemdanh timestamp with time zone
);


--
-- TOC entry 226 (class 1259 OID 19655)
-- Name: datgiai; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.datgiai (
    madatgiai character varying(50) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    madangkycanhan character varying(50),
    madangkydoi character varying(50),
    loaidangky character varying(20),
    tengiai character varying(100),
    giaithuong character varying(300),
    diemrenluyen numeric(5,2),
    ngaytrao timestamp with time zone,
    CONSTRAINT check_dangky_datgiai CHECK (((((loaidangky)::text = 'CaNhan'::text) AND (madangkycanhan IS NOT NULL) AND (madangkydoi IS NULL)) OR (((loaidangky)::text = 'DoiNhom'::text) AND (madangkydoi IS NOT NULL) AND (madangkycanhan IS NULL)))),
    CONSTRAINT datgiai_loaidangky_check CHECK (((loaidangky)::text = ANY (ARRAY[('CaNhan'::character varying)::text, ('DoiNhom'::character varying)::text])))
);



--
-- TOC entry 227 (class 1259 OID 19662)
-- Name: dethi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dethi (
    madethi character varying(50) NOT NULL,
    tendethi character varying(300) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    loaidethi character varying(50),
    filedethi character varying(500),
    thoigianlambai integer,
    diemtoida numeric(5,2),
    ngaytao timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    nguoitao character varying(50),
    trangthai character varying(50) DEFAULT 'Draft'::character varying
);



--
-- TOC entry 228 (class 1259 OID 19669)
-- Name: diemdanhlichhoc; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.diemdanhlichhoc (
    madiemdanh character varying(50) NOT NULL,
    malichhoc character varying(50) NOT NULL,
    masinhvien character varying(50) NOT NULL,
    ngayhoc date NOT NULL,
    trangthai character varying(20),
    ghichu text,
    thoigiandiemdanh timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT diemdanhlichhoc_trangthai_check CHECK (((trangthai)::text = ANY (ARRAY[('CoMat'::character varying)::text, ('VangMat'::character varying)::text, ('VangCoPhep'::character varying)::text, ('DiTre'::character varying)::text])))
);


--
-- TOC entry 229 (class 1259 OID 19676)
-- Name: diemdanhqr; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.diemdanhqr (
    madiemdanh character varying(50) NOT NULL,
    mahoatdong character varying(50),
    macuocthi character varying(50),
    masinhvien character varying(50) NOT NULL,
    maqr character varying(200),
    thoigiandiemdanh timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    vitri character varying(300)
);



--
-- TOC entry 230 (class 1259 OID 19682)
-- Name: diemrenluyen; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.diemrenluyen (
    madiemrl character varying(50) NOT NULL,
    masinhvien character varying(50) NOT NULL,
    macuocthi character varying(50),
    mahoatdong character varying(50),
    loaihoatdong character varying(50),
    diem numeric(5,2),
    mota text,
    ngaycong timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    magangiai character varying(50),
    CONSTRAINT diemrenluyen_loaihoatdong_check CHECK (((loaihoatdong)::text = ANY (ARRAY[('DuThi'::character varying)::text, ('HoTro'::character varying)::text, ('DatGiai'::character varying)::text])))
);



--
-- TOC entry 231 (class 1259 OID 19689)
-- Name: doithi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.doithi (
    madoithi character varying(50) NOT NULL,
    tendoithi character varying(200) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    matruongdoi character varying(50),
    sothanhvien integer DEFAULT 0,
    ngaydangky timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    trangthai character varying(50) DEFAULT 'Active'::character varying
);



--
-- TOC entry 249 (class 1259 OID 20191)
-- Name: gangiaithuong; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.gangiaithuong (
    magangiai character varying(50) NOT NULL,
    macocau character varying(50) NOT NULL,
    madangkycanhan character varying(50),
    madangkydoi character varying(50),
    loaidangky character varying(20),
    ladonghang boolean DEFAULT false,
    xephangthucte integer,
    trangthai character varying(20) DEFAULT 'Pending'::character varying,
    nguoiduyet character varying(50),
    ngayduyet timestamp with time zone,
    ghichu text,
    nguoigan character varying(50),
    ngaygan timestamp with time zone,
    CONSTRAINT check_dangky_gangiai CHECK (((((loaidangky)::text = 'CaNhan'::text) AND (madangkycanhan IS NOT NULL) AND (madangkydoi IS NULL)) OR (((loaidangky)::text = 'DoiNhom'::text) AND (madangkydoi IS NOT NULL) AND (madangkycanhan IS NULL)))),
    CONSTRAINT gangiaithuong_loaidangky_check CHECK (((loaidangky)::text = ANY ((ARRAY['CaNhan'::character varying, 'DoiNhom'::character varying])::text[]))),
    CONSTRAINT gangiaithuong_trangthai_check CHECK (((trangthai)::text = ANY ((ARRAY['Pending'::character varying, 'Approved'::character varying, 'Rejected'::character varying])::text[])))
);


--
-- TOC entry 232 (class 1259 OID 19695)
-- Name: giangvien; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.giangvien (
    magiangvien character varying(50) NOT NULL,
    manguoidung character varying(50) NOT NULL,
    mabomon character varying(50),
    chucvu character varying(100),
    hocvi character varying(50),
    chuyenmon character varying(200),
    is_admin boolean DEFAULT false NOT NULL
);

--
-- TOC entry 233 (class 1259 OID 19700)
-- Name: hoatdonghotro; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.hoatdonghotro (
    mahoatdong character varying(50) NOT NULL,
    tenhoatdong character varying(300) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    loaihoatdong character varying(50),
    diemrenluyen numeric(5,2),
    thoigianbatdau timestamp with time zone,
    thoigianketthuc timestamp with time zone,
    diadiem character varying(300),
    mota text,
    soluong integer DEFAULT 20,
    CONSTRAINT hoatdonghotro_loaihoatdong_check CHECK (((loaihoatdong)::text = ANY (ARRAY[('CoVu'::character varying)::text, ('ToChuc'::character varying)::text, ('HoTroKyThuat'::character varying)::text])))
);


--
-- TOC entry 234 (class 1259 OID 19707)
-- Name: kehoachcuocthi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kehoachcuocthi (
    makehoach character varying(50) NOT NULL,
    namhoc character varying(20) NOT NULL,
    hocky character varying(10),
    trangthaiduyet character varying(50) DEFAULT 'Pending'::character varying,
    ngaynopkehoach timestamp with time zone,
    ngayduyet timestamp with time zone,
    nguoiduyet character varying(50),
    ghichu text,
    tencuocthi character varying(300),
    loaicuocthi character varying(50),
    mota text,
    mucdich text,
    doituongthamgia character varying(200),
    thoigianbatdau timestamp with time zone,
    thoigianketthuc timestamp with time zone,
    diadiem character varying(300),
    soluongthanhvien integer,
    hinhthucthamgia character varying(50),
    dutrukinhphi numeric(15,2),
    mabomon character varying(50),
    nguoinop character varying(50),
    CONSTRAINT chk_kehoach_hinhthucthamgia CHECK (((hinhthucthamgia)::text = ANY ((ARRAY['CaNhan'::character varying, 'DoiNhom'::character varying, 'CaHai'::character varying])::text[]))),
    CONSTRAINT chk_kehoach_loaicuocthi CHECK (((loaicuocthi)::text = ANY ((ARRAY['CuocThi'::character varying, 'Seminar'::character varying, 'HoiThao'::character varying])::text[]))),
    CONSTRAINT kehoachcuocthi_trangthaiduyet_check CHECK (((trangthaiduyet)::text = ANY (ARRAY[('Pending'::character varying)::text, ('Approved'::character varying)::text, ('Rejected'::character varying)::text])))
);

--
-- TOC entry 235 (class 1259 OID 19714)
-- Name: ketquathi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ketquathi (
    maketqua character varying(50) NOT NULL,
    mabaithi character varying(50) NOT NULL,
    diem numeric(5,2),
    xephang integer,
    giaithuong character varying(100),
    nhanxet text,
    ngaychamdiem timestamp with time zone,
    nguoichamdiem character varying(50)
);



--
-- TOC entry 236 (class 1259 OID 19719)
-- Name: lichhoc; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lichhoc (
    malichhoc character varying(50) NOT NULL,
    mamonhoc character varying(50) NOT NULL,
    malop character varying(50),
    magiangvien character varying(50),
    thu character varying(20),
    tietbatdau integer,
    tietketthuc integer,
    phonghoc character varying(50),
    ngaybatdau date,
    ngayketthuc date,
    ghichu text,
    CONSTRAINT check_tiet CHECK ((tietketthuc >= tietbatdau)),
    CONSTRAINT lichhoc_thu_check CHECK (((thu)::text = ANY (ARRAY[('Thu2'::character varying)::text, ('Thu3'::character varying)::text, ('Thu4'::character varying)::text, ('Thu5'::character varying)::text, ('Thu6'::character varying)::text, ('Thu7'::character varying)::text, ('ChuNhat'::character varying)::text]))),
    CONSTRAINT lichhoc_tietbatdau_check CHECK (((tietbatdau >= 1) AND (tietbatdau <= 16))),
    CONSTRAINT lichhoc_tietketthuc_check CHECK (((tietketthuc >= 1) AND (tietketthuc <= 16)))
);



--
-- TOC entry 237 (class 1259 OID 19728)
-- Name: lop; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.lop (
    malop character varying(50) NOT NULL,
    tenlop character varying(200) NOT NULL,
    nienkhoa character varying(20),
    soluongsinhvien integer DEFAULT 0,
    magiangvienchunhiem character varying(50)
);



--
-- TOC entry 247 (class 1259 OID 20143)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- TOC entry 246 (class 1259 OID 20142)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;



--
-- TOC entry 5184 (class 0 OID 0)
-- Dependencies: 246
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 238 (class 1259 OID 19732)
-- Name: monhoc; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.monhoc (
    mamonhoc character varying(50) NOT NULL,
    tenmonhoc character varying(200) NOT NULL,
    sotinchi integer,
    mabomon character varying(50),
    mota text
);


--
-- TOC entry 239 (class 1259 OID 19737)
-- Name: nguoidung; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.nguoidung (
    manguoidung character varying(50) NOT NULL,
    tendangnhap character varying(100) NOT NULL,
    matkhau character varying(255) NOT NULL,
    hoten character varying(200) NOT NULL,
    email character varying(200) NOT NULL,
    sodienthoai character varying(20),
    vaitro character varying(50) NOT NULL,
    trangthai character varying(20) DEFAULT 'Active'::character varying,
    ngaytao timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    ngaycapnhat timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    anhdaidien character varying(255),
    CONSTRAINT nguoidung_vaitro_check CHECK (((vaitro)::text = ANY (ARRAY[('Admin'::character varying)::text, ('GiangVien'::character varying)::text, ('SinhVien'::character varying)::text])))
);



--
-- TOC entry 251 (class 1259 OID 20239)
-- Name: password_reset_otps; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_otps (
    id bigint NOT NULL,
    email character varying(255) NOT NULL,
    otp character varying(6) NOT NULL,
    created_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    is_used boolean DEFAULT false NOT NULL
);



--
-- TOC entry 250 (class 1259 OID 20238)
-- Name: password_reset_otps_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.password_reset_otps_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 5185 (class 0 OID 0)
-- Dependencies: 250
-- Name: password_reset_otps_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.password_reset_otps_id_seq OWNED BY public.password_reset_otps.id;


--
-- TOC entry 240 (class 1259 OID 19746)
-- Name: phanconggiangvien; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.phanconggiangvien (
    maphancong character varying(50) NOT NULL,
    magiangvien character varying(50) NOT NULL,
    macongviec character varying(50) NOT NULL,
    maban character varying(50),
    vaitro character varying(100),
    ngayphancong timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);



--
-- TOC entry 241 (class 1259 OID 19750)
-- Name: quyettoan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.quyettoan (
    maquyettoan character varying(50) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    tongdutru numeric(15,2),
    tongthucte numeric(15,2),
    chenhlech numeric(15,2),
    ngayquyettoan date,
    nguoilap character varying(50),
    nguoiduyet character varying(50),
    trangthai character varying(50) DEFAULT 'Draft'::character varying,
    filequyettoan character varying(500),
    ghichu text
);


--
-- TOC entry 242 (class 1259 OID 19756)
-- Name: sinhvien; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sinhvien (
    masinhvien character varying(50) NOT NULL,
    manguoidung character varying(50) NOT NULL,
    malop character varying(50),
    namnhaphoc integer,
    diemrenluyen numeric(5,2) DEFAULT 70.00,
    trangthai character varying(20) DEFAULT 'Active'::character varying
);



--
-- TOC entry 243 (class 1259 OID 19761)
-- Name: thanhviendoithi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.thanhviendoithi (
    mathanhvien character varying(50) NOT NULL,
    madoithi character varying(50) NOT NULL,
    masinhvien character varying(50) NOT NULL,
    vaitro character varying(50),
    ngaythamgia timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT thanhviendoithi_vaitro_check CHECK (((vaitro)::text = ANY (ARRAY[('TruongDoi'::character varying)::text, ('ThanhVien'::character varying)::text])))
);



--
-- TOC entry 244 (class 1259 OID 19766)
-- Name: tintuc; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tintuc (
    matintuc character varying(50) NOT NULL,
    tieude character varying(500) NOT NULL,
    noidung text,
    macuocthi character varying(50),
    loaitin character varying(50),
    hinhanh character varying(500),
    tacgia character varying(50),
    luotxem integer DEFAULT 0,
    trangthai character varying(50) DEFAULT 'Published'::character varying,
    ngaydang timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    ngaycapnhat timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT tintuc_loaitin_check CHECK (((loaitin)::text = ANY (ARRAY[('ThongBao'::character varying)::text, ('TinTuc'::character varying)::text, ('SuKien'::character varying)::text])))
);



--
-- TOC entry 245 (class 1259 OID 19776)
-- Name: vongthi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vongthi (
    mavongthi character varying(50) NOT NULL,
    tenvongthi character varying(200) NOT NULL,
    macuocthi character varying(50) NOT NULL,
    thutu integer,
    thoigianbatdau timestamp with time zone,
    thoigianketthuc timestamp with time zone,
    diadiem character varying(300),
    mota text,
    trangthai character varying(50) DEFAULT 'Upcoming'::character varying
);



--
-- TOC entry 4808 (class 2604 OID 20146)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 4817 (class 2604 OID 20242)
-- Name: password_reset_otps id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_otps ALTER COLUMN id SET DEFAULT nextval('public.password_reset_otps_id_seq'::regclass);


--
-- TOC entry 5144 (class 0 OID 19592)
-- Dependencies: 217
-- Data for Name: baithi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.baithi (mabaithi, madethi, madangkycanhan, madangkydoi, loaidangky, filebaithi, thoigiannop, trangthai) FROM stdin;
BT001	DE001	\N	DKDT001	DoiNhom	/uploads/baithi/DT001_COATI_Research.pdf	2024-06-15 16:00:00+07	Submitted
BT002	DE002	DKCN003	\N	CaNhan	/uploads/baithi/2001221874_Olympic.cpp	2024-04-30 15:30:00+07	Submitted
BT003	DE002	\N	DKDT002	DoiNhom	/uploads/baithi/DT002_Olympic.cpp	2024-04-30 15:45:00+07	Submitted
BT004	DE003	\N	DKDT005	DoiNhom	/uploads/baithi/DT005_WebMasters_Project.zip	2025-09-28 16:00:00+07	Submitted
BT17637492583959	DE006	DKCNHHLGTJ2O	\N	CaNhan	baithis/CT006_2001221872_LeTrungKien_BT17637492583959.pdf	2025-11-21 18:20:58+07	Submitted
BT17644437158120	DT0001	DKCNTKQYERJN	\N	CaNhan	baithis/CT0013_2001221872_LeTrungKien_BT17644437158120.pdf	2025-11-29 19:15:15+07	Submitted
BT17644459653120	DT0002	DKCNS59EKFVQ	\N	CaNhan	baithis/CT0014_2001221872_LeTrungKien_BT17644459653120.pdf	2025-11-29 19:52:45+07	Submitted
BT17651420572391	DT0004	DKCN47WDPY8H	\N	CaNhan	baithis/CT0016_2001221872_LeTrungKien_BT17651420572391.pdf	2025-12-07 21:14:17+07	Submitted
\.


--
-- TOC entry 5145 (class 0 OID 19600)
-- Dependencies: 218
-- Data for Name: ban; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ban (maban, tenban, macuocthi, mota) FROM stdin;
BAN001	Ban Học thuật	CT001	Phụ trách nội dung học thuật và chấm điểm
BAN002	Ban Tổ chức	CT001	Phụ trách tổ chức và vận hành cuộc thi
BAN003	Ban Chuyên môn	CT002	Phụ trách nội dung hội thảo
BAN004	Ban Giám khảo	CT003	Chấm điểm và đánh giá bài thi
BAN005	Ban Đề thi	CT004	Ban soạn đề thi vòng sơ khảo và chung kết
BAN006	Ban Giám khảo Sơ khảo	CT004	Ban chấm điểm vòng sơ khảo
BAN007	Ban Giám khảo Chung kết	CT004	Ban chấm điểm vòng chung kết
BAN008	Ban Học thuật Hackathon	CT005	Phụ trách nội dung và chấm điểm
BAN009	Ban Hậu cần Hackathon	CT005	Phụ trách logistics và hỗ trợ kỹ thuật
BAN010	Ban Đề thi	CT0013	dd
BAN011	Ban Học thuật	CT0014	dd
BAN012	Ban Học thuật	CT006	sad
BAN013	Ban Học thuật	CT0015	xfg
BAN014	Ban Học thuật	CT0016	ghdhdh
BAN015	Ban Học thuật	CT0018	Ban Học thuật là bộ phận quan trọng nhất về mặt chuyên môn của HRIC 2026, chịu trách nhiệm đảm bảo chất lượng khoa học, tính công bằng và tính học thuật của toàn bộ cuộc thi.
BAN016	Ban Tổ chức	CT0018	Ban Tổ chức (BTC) là “bộ não” điều hành toàn bộ cuộc thi, chịu trách nhiệm lập kế hoạch, vận hành và đảm bảo HRIC 2026 diễn ra chuyên nghiệp, đúng tiến độ và để lại dấu ấn lớn trong trường.
BAN017	Ban Đề thi	CT0018	Ban Đề thi (hay còn gọi là Ban Câu hỏi Phản biện / Ban Ra đề Phản biện) là ban chuyên môn đặc biệt, chịu trách nhiệm chuẩn bị toàn bộ câu hỏi phản biện dành cho vòng Chung khảo (thuyết trình bảo vệ trực tiếp). Đây là “vũ khí” quyết định độ khó, độ sâu và tính học thuật của phần bảo vệ.
BAN018	Ban Học thuật	CT0019	Ban Học thuật là bộ phận quan trọng nhất về mặt chuyên môn của HRIC 2026, chịu trách nhiệm đảm bảo chất lượng khoa học, tính công bằng và tính học thuật của toàn bộ cuộc thi.
BAN019	Ban Tổ chức	CT0019	Ban Tổ chức (BTC) là “bộ não” điều hành toàn bộ cuộc thi, chịu trách nhiệm lập kế hoạch, vận hành và đảm bảo HRIC 2026 diễn ra chuyên nghiệp, đúng tiến độ và để lại dấu ấn lớn trong trường.
BAN020	Ban Đề thi	CT0019	Ban Đề thi (hay còn gọi là Ban Câu hỏi Phản biện / Ban Ra đề Phản biện) là ban chuyên môn đặc biệt, chịu trách nhiệm chuẩn bị toàn bộ câu hỏi phản biện dành cho vòng Chung khảo (thuyết trình bảo vệ trực tiếp). Đây là “vũ khí” quyết định độ khó, độ sâu và tính học thuật của phần bảo vệ.
BAN021	Ban Đề thi	CT0021	cham diem
\.


--
-- TOC entry 5146 (class 0 OID 19605)
-- Dependencies: 219
-- Data for Name: bomon; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.bomon (mabomon, tenbomon, matruongbomon, mota) FROM stdin;
KHMT	Khoa Công nghệ Thông tin	GV002	Khoa đào tạo về Công nghệ thông tin và Khoa học máy tính
HTTT	Khoa Hệ thống Thông tin	GV002	Khoa đào tạo về Hệ thống thông tin quản lý
CNPM	Khoa Công nghệ Phần mềm	GV002	Khoa đào tạo về Công nghệ phần mềm
KHDL	Khoa Khoa học Dữ liệu	GV002	Khoa đào tạo về Khoa học dữ liệu và Trí tuệ nhân tạo
\.


--
-- TOC entry 5147 (class 0 OID 19610)
-- Dependencies: 220
-- Data for Name: chiphi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.chiphi (machiphi, macuocthi, tenkhoanchi, dutruchiphi, thuctechi, ngaychi, nguoiduyet, trangthai, chungtu, ghichu, nguoiyeucau, ngayyeucau, ngayduyet, magangiai) FROM stdin;
CP001	CT001	Giải thưởng	10000000.00	10000000.00	2024-06-30	GV002	Approved	\N	Giải thưởng cho các đội đạt giải	\N	\N	\N	\N
CP002	CT001	Văn phòng phẩm	2000000.00	1800000.00	2024-03-15	GV002	Approved	\N	Mua văn phòng phẩm cho cuộc thi	\N	\N	\N	\N
CP003	CT002	Tiền trà, nước uống	5000000.00	4800000.00	2024-05-15	GV001	Approved	\N	Chi phí phục vụ hội thảo	\N	\N	\N	\N
CP004	CT003	Giải thưởng Olympic	15000000.00	15000000.00	2024-05-10	GV002	Approved	\N	Giải thưởng Olympic Tin học	\N	\N	\N	\N
CP005	CT003	Thuê địa điểm	8000000.00	7500000.00	2024-04-01	GV002	Approved	\N	Thuê phòng thi	\N	\N	\N	\N
CP007	CT004	Văn phòng phẩm và in ấn	1500000.00	\N	\N	\N	Pending	\N	In đề thi, giấy làm bài	\N	\N	\N	\N
CP008	CT004	Nước uống và refreshment	2500000.00	\N	\N	\N	Pending	\N	Cho giám khảo và giảng viên	\N	\N	\N	\N
CP009	CT005	Giải thưởng	15000000.00	\N	\N	\N	Pending	\N	Giải Nhất: 8tr, Nhì: 5tr, Ba: 2tr	\N	\N	\N	\N
CP010	CT005	Thuê thiết bị	5000000.00	\N	\N	\N	Pending	\N	Máy chiếu, âm thanh, wifi	\N	\N	\N	\N
CP011	CT005	Ăn uống cho thí sinh	5000000.00	\N	\N	\N	Pending	\N	3 ngày thi	\N	\N	\N	\N
CP012	CT006	Giải thưởng	12000000.00	\N	\N	\N	Pending	\N	Giải Vàng, Bạc, Đồng và Khuyến khích	\N	\N	\N	\N
CP013	CT006	Vận hành hệ thống	3000000.00	\N	\N	\N	Pending	\N	Server chấm bài online	\N	\N	\N	\N
CP014	CT006	Văn phòng phẩm	3000000.00	\N	\N	\N	Pending	\N	In giấy khen, tài liệu	\N	\N	\N	\N
CP015	CT007	Tiền diễn giả	10000000.00	10000000.00	2025-09-25	GV002	Approved	\N	Chi phí mời 3 diễn giả	\N	\N	\N	\N
CP016	CT007	In tài liệu hội thảo	5000000.00	4800000.00	2025-09-20	GV002	Approved	\N	300 bộ tài liệu	\N	\N	\N	\N
CP017	CT007	Ăn trưa cho khách mời	7000000.00	6900000.00	2025-09-25	GV002	Approved	\N	Tiệc trưa buffet	\N	\N	\N	\N
CP018	CT008	Giải thưởng	10000000.00	10000000.00	2025-09-30	GV002	Approved	\N	Giải Nhất: 5tr, Nhì: 3tr, Ba: 2tr	\N	\N	\N	\N
CP019	CT008	Hosting và Domain	3000000.00	2500000.00	2025-08-20	GV002	Approved	\N	Cho các đội demo	\N	\N	\N	\N
CP020	CT008	Văn phòng phẩm	3000000.00	2800000.00	2025-09-28	GV002	Approved	\N	In giấy khen, băng rôn	\N	\N	\N	\N
CP006	CT004	Giải thưởng	8000000.00	\N	\N	GV002	Approved	\N	Giải Nhất: 3tr, Nhì: 2tr, Ba: 1.5tr x2	\N	\N	\N	\N
CP0025	CT0016	sssdf	100000.00	90000.00	2025-12-08	GV002	Approved	chungtu/1765148661_test.pdf	sdf	GV001	2025-12-07	2025-12-07	\N
CP0021	CT0012	đ	100000.00	10000.00	2025-11-28	GV002	Approved	chungtu/1764271648_cuoc_thi - public.png	ff	GV001	2025-11-27	2025-11-27	\N
CP0026	CT0018	Tiền thưởng: Giải nhất (1 giải)	1000000.00	1000000.00	2025-12-10	GV002	Approved	\N	Chi phí giải thưởng được tạo và phê duyệt tự động từ cơ cấu giải thưởng.\n- Tên giải: Giải nhất\n- Số lượng: 1 giải\n- Tiền thưởng/giải: 1.000.000 VNĐ\n- Tổng chi phí: 1.000.000 VNĐ\n- Kèm giấy khen	GV002	2025-12-10	2025-12-10	\N
CP0027	CT0018	Tiền thưởng: Giải Nhì (2 giải)	1000000.00	1000000.00	2025-12-10	GV002	Approved	\N	Chi phí giải thưởng được tạo và phê duyệt tự động từ cơ cấu giải thưởng.\n- Tên giải: Giải Nhì\n- Số lượng: 2 giải\n- Tiền thưởng/giải: 500.000 VNĐ\n- Tổng chi phí: 1.000.000 VNĐ\n- Kèm giấy khen	GV002	2025-12-10	2025-12-10	\N
CP0022	CT006	sss	10000.00	9000.00	2025-11-28	GV002	Approved	chungtu/1764275577_cuoc_thi - public.png	ss	GV001	2025-11-27	2025-11-27	\N
CP0029	CT0018	Tiền thưởng: Giải Ba (3 giải)	600000.00	600000.00	2025-12-10	GV002	Approved	\N	Chi phí giải thưởng được tạo và phê duyệt tự động từ cơ cấu giải thưởng.\n- Tên giải: Giải Ba\n- Số lượng: 3 giải\n- Tiền thưởng/giải: 200.000 VNĐ\n- Tổng chi phí: 600.000 VNĐ\n- Kèm giấy khen	GV002	2025-12-10	2025-12-10	\N
CP0030	CT0019	Tiền thưởng: Giải nhất (1 giải)	5000000.00	5000000.00	2025-12-10	GV002	Approved	\N	Chi phí giải thưởng được tạo và phê duyệt tự động từ cơ cấu giải thưởng.\n- Tên giải: Giải nhất\n- Số lượng: 1 giải\n- Tiền thưởng/giải: 5.000.000 VNĐ\n- Tổng chi phí: 5.000.000 VNĐ\n- Kèm giấy khen	GV002	2025-12-10	2025-12-10	\N
CP0024	CT0016	Tiền thưởng: Giải nhất (1 giải)	100000.00	100000.00	2025-12-07	GV002	Approved	chungtu/1765148161_test.pdf	Chi phí giải thưởng được tạo và phê duyệt tự động từ cơ cấu giải thưởng.\r\n- Tên giải: Giải nhất\r\n- Số lượng: 1 giải\r\n- Tiền thưởng/giải: 100.000 VNĐ\r\n- Tổng chi phí: 100.000 VNĐ\r\n- Kèm giấy khen	GV002	2025-12-07	2025-12-07	\N
\.


--
-- TOC entry 5175 (class 0 OID 20172)
-- Dependencies: 248
-- Data for Name: cocaugiaithuong; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cocaugiaithuong (macocau, macuocthi, tengiai, soluong, tienthuong, giaykhen, chophepdonghang, ghichudonghang, ghichu, trangthai, ngaytao) FROM stdin;
COCAU-DMFLRI4D	CT0013	Giải nhất	1	10000.00	t	f	\N	\N	Active	2025-11-29 19:29:06+07
COCAU-3HW6AA5U	CT0014	Giải Nhất	1	100000.00	t	f	\N	dđ	Active	2025-11-29 19:48:17+07
COCAU-X561G8BW	CT0014	Giải Nhì	1	100000.00	t	f	\N	\N	Active	2025-12-01 19:35:15+07
COCAU-96AGG4VM	CT0016	Giải nhất	1	100000.00	t	f	\N	đá	Active	2025-12-07 22:53:43+07
COCAU-FXJSAY1F	CT0018	Giải nhất	1	1000000.00	t	f	\N	giải nhất	Active	2025-12-10 16:15:59+07
COCAU-ZDPUCGZU	CT0018	Giải Nhì	2	500000.00	t	f	\N	giải nhì	Active	2025-12-10 16:16:46+07
COCAU-7ELSM5TG	CT0018	Giải Ba	3	200000.00	t	f	\N	giải ba	Active	2025-12-10 16:22:38+07
COCAU-LP8YSDKD	CT0019	Giải nhất	1	5000000.00	t	f	\N	giai nhat	Active	2025-12-10 16:54:16+07
\.


--
-- TOC entry 5148 (class 0 OID 19616)
-- Dependencies: 221
-- Data for Name: congviec; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.congviec (macongviec, tencongviec, maban, macuocthi, mota, thoigianbatdau, thoigianketthuc, trangthai) FROM stdin;
CV001	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT001	Xây dựng đề tài cuộc thi và bộ tiêu chí chấm điểm	2024-02-20 08:00:00+07	2024-03-01 17:00:00+07	Completed
CV002	Tổ chức lễ khai mạc	BAN002	CT001	Chuẩn bị và tổ chức lễ khai mạc cuộc thi	2024-03-01 08:00:00+07	2024-03-01 10:00:00+07	Completed
CV003	Soạn đề thi Database Challenge	BAN005	CT004	Soạn đề thi sơ khảo và chung kết	2025-11-10 08:00:00+07	2025-11-25 17:00:00+07	Completed
CV004	Chấm điểm vòng sơ khảo	BAN006	CT004	Chấm bài thi trắc nghiệm vòng sơ khảo	2025-12-07 09:00:00+07	2025-12-07 11:00:00+07	Pending
CV005	Chấm điểm vòng chung kết	BAN007	CT004	Chấm bài thực hành vòng chung kết	2025-12-07 14:30:00+07	2025-12-07 16:00:00+07	Pending
CV006	Hỗ trợ kỹ thuật Hackathon	BAN009	CT005	Hỗ trợ kỹ thuật và giải đáp thắc mắc	2025-10-15 08:00:00+07	2025-10-17 18:00:00+07	InProgress
CV007	Đánh giá dự án Hackathon	BAN008	CT005	Đánh giá và chấm điểm các dự án	2025-10-17 14:00:00+07	2025-10-17 18:00:00+07	Pending
CV008	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT002	Xây dựng đề tài Hội thảo Khoa học Dữ liệu 2024 và tiêu chí tham gia	2024-03-01 08:00:00+07	2024-04-20 17:00:00+07	Completed
CV009	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT003	Xây dựng nội dung và tiêu chí chấm Olympic Tin học Sinh viên 2024	2024-01-15 08:00:00+07	2024-03-20 17:00:00+07	Completed
CV010	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT004	Xây dựng đề tài Database Design Challenge 2025 và bộ tiêu chí chấm điểm	2025-09-01 08:00:00+07	2025-11-20 17:00:00+07	Completed
CV011	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT005	Xây dựng chủ đề Hackathon AI 2025 - Smart City và bộ tiêu chí chấm dự án	2025-06-15 08:00:00+07	2025-09-25 17:00:00+07	Completed
CV013	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT007	Xây dựng nội dung và danh sách diễn giả Hội thảo An toàn Thông tin 2025	2025-06-20 08:00:00+07	2025-09-10 17:00:00+07	Completed
CV014	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT008	Xây dựng đề tài Thiết kế Website 2025 và bộ rubric chấm điểm	2025-05-01 08:00:00+07	2025-08-01 17:00:00+07	Completed
CV015	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT009	Xây dựng đề tài Phát triển Ứng dụng Di động 2025 và tiêu chí chấm	2025-04-01 08:00:00+07	2025-06-20 17:00:00+07	Completed
CV016	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT010	Xây dựng chủ đề Game Development Challenge 2025 và tiêu chí đánh giá	2025-07-01 08:00:00+07	2025-09-20 17:00:00+07	InProgress
CV017	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT011	Xây dựng chủ đề IoT Innovation Contest 2025 và bộ tiêu chí chấm dự án IoT	2025-06-01 08:00:00+07	2025-08-20 17:00:00+07	Completed
CV012	Xây dựng đề tài và tiêu chí đánh giá	BAN001	CT006	Xây dựng đề thi Olympic Tin học Sinh viên 2025 (lập trình, thuật toán)	2025-11-19 08:00:00+07	2025-11-30 17:00:00+07	Completed
CV018	Chấm điểm vòng chung kết	BAN010	CT0013	\N	\N	\N	Pending
CV019	Chấm điểm vòng chung kết	BAN011	CT0014	\N	\N	\N	Pending
CV020	Chấm điểm vòng chung kết	BAN014	CT0016	\N	\N	\N	Pending
CV021	Chấm điểm	BAN014	CT0016	\N	\N	\N	Pending
CV022	Xây dựng đề tài và tiêu chí đánh giá	BAN015	CT0018	\N	\N	\N	Pending
CV023	Hỗ trợ kỹ thuật	BAN016	CT0018	\N	\N	\N	Pending
CV024	Chấm điểm	BAN017	CT0018	\N	\N	\N	Pending
CV025	Xây dựng đề tài và tiêu chí đánh giá	BAN018	CT0019	\N	\N	\N	Pending
CV026	Đánh giá dự án	BAN018	CT0019	\N	\N	\N	Pending
CV027	Chấm điểm	BAN020	CT0019	\N	\N	\N	Pending
CV028	Chấm điểm	BAN021	CT0021	\N	\N	\N	Pending
\.


--
-- TOC entry 5149 (class 0 OID 19622)
-- Dependencies: 222
-- Data for Name: cuocthi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cuocthi (macuocthi, tencuocthi, loaicuocthi, mota, mucdich, doituongthamgia, thoigianbatdau, thoigianketthuc, diadiem, soluongthanhvien, hinhthucthamgia, trangthai, dutrukinhphi, chiphithucte, mabomon, ngaytao, ngaycapnhat, makehoach) FROM stdin;
CT001	Ứng dụng thuật toán COATI để cải tiến gom mờ trong phân loại khách hàng	CuocThi	Nâng cao hiệu quả phân cụm khách hàng bằng cách cải tiến thuật toán Fuzzy C-Means (FCM), khắc phục các hạn chế về độ chính xác, tốc độ hội tụ và nguy cơ rơi vào cực tiểu cục bộ.	Nghiên cứu và ứng dụng thuật toán COATI-FCM trong phân tích khách hàng	Sinh viên khoa CNTT	2024-03-01 08:00:00+07	2024-06-30 17:00:00+07	Trường Đại học Công nghệ Thông tin - ĐHQG TP.HCM	3	DoiNhom	InProgress	15000000.00	11800000.00	KHDL	2025-11-17 02:29:37.895936+07	2025-11-17 02:29:37.895936+07	\N
CT002	Hội thảo Khoa học Dữ liệu 2024	HoiThao	Hội thảo trao đổi về các công nghệ mới trong lĩnh vực khoa học dữ liệu	Cập nhật kiến thức và kỹ năng mới	Sinh viên và giảng viên	2024-05-15 08:00:00+07	2024-05-15 17:00:00+07	Hội trường A, UIT	200	CaNhan	Approved	20000000.00	4800000.00	KHDL	2025-11-17 02:29:37.895936+07	2025-11-17 02:29:37.895936+07	\N
CT003	Olympic Tin học Sinh viên 2024	CuocThi	Cuộc thi lập trình thuật toán dành cho sinh viên	Phát triển tư duy thuật toán và kỹ năng lập trình	Sinh viên các trường đại học	2024-04-01 08:00:00+07	2024-04-30 17:00:00+07	UIT	1	CaHai	Approved	30000000.00	22500000.00	KHMT	2025-11-17 02:29:37.895936+07	2025-11-17 02:29:37.895936+07	\N
CT010	Cuộc thi Phát triển Game 2025	CuocThi	Cuộc thi thiết kế và phát triển game 2D/3D với nội dung sáng tạo, gameplay hấp dẫn	Phát triển tư duy sáng tạo và kỹ năng lập trình game cho sinh viên	Sinh viên các khoa	2025-10-01 08:00:00+07	2025-11-30 17:00:00+07	Online + Demo tại B Building	5	DoiNhom	InProgress	18000000.00	0.00	CNPM	2025-11-17 02:41:36.226949+07	2025-11-17 02:41:36.226949+07	\N
CT007	Hội thảo An toàn Thông tin 2025	HoiThao	Hội thảo về các xu hướng và thách thức trong lĩnh vực an toàn thông tin, mạng máy tính và bảo mật dữ liệu	Cập nhật kiến thức về an toàn thông tin, trao đổi kinh nghiệm thực tiễn	Sinh viên và giảng viên các khoa CNTT	2025-09-25 08:00:00+07	2025-09-25 17:00:00+07	Hội trường C - HUIT	300	CaNhan	Completed	22000000.00	21700000.00	CNPM	2025-11-17 02:41:36.226949+07	2025-11-17 02:41:36.226949+07	\N
CT008	Cuộc thi Thiết kế Website 2025	CuocThi	Cuộc thi thiết kế và phát triển website với giao diện đẹp, tính năng hiện đại và trải nghiệm người dùng tốt	Phát triển kỹ năng thiết kế web, frontend và backend cho sinh viên	Sinh viên năm 2, 3, 4 các ngành CNTT	2025-08-15 08:00:00+07	2025-09-30 17:00:00+07	Online + Phòng A204	3	DoiNhom	Completed	16000000.00	15300000.00	CNPM	2025-11-17 02:41:36.226949+07	2025-11-17 02:41:36.226949+07	\N
CT009	Cuộc thi Phát triển Ứng dụng Di động 2025	CuocThi	Cuộc thi phát triển ứng dụng di động trên nền tảng Android/iOS giải quyết các vấn đề thực tế	Khuyến khích sinh viên phát triển ứng dụng di động sáng tạo và hữu ích	Sinh viên các ngành CNTT, CNPM	2025-07-01 08:00:00+07	2025-08-31 17:00:00+07	Online + Demo tại A209	4	DoiNhom	Completed	20000000.00	0.00	CNPM	2025-11-17 02:41:36.226949+07	2025-11-17 02:41:36.226949+07	\N
CT011	Cuộc thi Đổi mới Sáng tạo IoT 2025	CuocThi	Cuộc thi phát triển các giải pháp IoT (Internet of Things) ứng dụng trong nông nghiệp thông minh, nhà thông minh, y tế	Khuyến khích sinh viên nghiên cứu và phát triển các giải pháp IoT thiết thực	Sinh viên các ngành CNTT, ATTT, KHDL	2025-09-01 08:00:00+07	2025-10-31 17:00:00+07	Lab IoT - C Building	4	DoiNhom	InProgress	22000000.00	0.00	HTTT	2025-11-17 02:41:36.226949+07	2025-11-17 02:41:36.226949+07	\N
CT005	Hackathon AI 2025 - Giải pháp Smart City	CuocThi	Cuộc thi phát triển giải pháp AI cho thành phố thông minh, tập trung vào các vấn đề thực tế như giao thông, môi trường, y tế	Khuyến khích sinh viên ứng dụng AI vào giải quyết các vấn đề của thành phố thông minh	Sinh viên các ngành CNTT, KHDL, ATTT	2025-10-20 08:00:00+07	2025-10-17 18:00:00+07	HUIT - B Building	4	DoiNhom	InProgress	25000000.00	0.00	KHDL	2025-11-17 02:41:36.226949+07	2025-11-17 02:41:36.226949+07	\N
CT004	Database Design Challenge 2025	CuocThi	Cuộc thi thiết kế cơ sở dữ liệu nhằm tạo cơ hội cho sinh viên vận dụng kiến thức về mô hình hóa dữ liệu, chuẩn hóa và tối ưu hóa CSDL vào giải quyết các bài toán thực tế	Rèn luyện kỹ năng phân tích yêu cầu nghiệp vụ, chuyển đổi thành cấu trúc CSDL hiệu quả và chính xác	Sinh viên năm 2, năm 3 các ngành Công nghệ thông tin, An toàn thông tin, Khoa học dữ liệu	2025-12-07 07:45:00+07	2025-12-07 16:30:00+07	Trường Đại học Công Thương TP.HCM	1	DoiNhom	Approved	12000000.00	0.00	KHMT	2025-11-17 02:41:19.532713+07	2025-11-17 02:41:19.532713+07	\N
CT006	Olympic Tin học Sinh viên 2025	CuocThi	Kỳ thi Olympic Tin học cấp trường dành cho sinh viên, bao gồm các nội dung lập trình, thuật toán, cấu trúc dữ liệu	Phát hiện và bồi dưỡng tài năng lập trình, chuẩn bị cho các kỳ thi Olympic cấp quốc gia	Sinh viên tất cả các khoa	2025-11-19 08:00:00+07	2025-11-21 15:00:00+07	Phòng máy A301, A302, A303	1	CaNhan	Completed	18000000.00	0.00	KHMT	2025-11-23 02:41:36.226+07	2025-11-17 02:41:36.226949+07	\N
CT0013	ssss	CuocThi	s	s	s	2025-11-19 02:57:00+07	2025-11-29 02:57:00+07	s	1	CaNhan	Completed	999998000.00	\N	KHMT	2025-11-29 19:11:04+07	2025-11-27 19:11:04+07	KH0016
CT0015	sádâd	CuocThi	đ	d	d	2025-12-02 01:57:00+07	2025-12-08 02:57:00+07	d	\N	CaNhan	Approved	100000.00	\N	KHMT	2025-12-02 18:58:03+07	2025-12-02 18:58:03+07	KH0017
CT0014	edg	CuocThi	s	s	d	2025-11-17 01:07:00+07	2025-11-29 04:07:00+07	s	100	CaNhan	Completed	10000000.00	\N	KHMT	2025-11-29 19:32:59+07	2025-11-29 19:32:59+07	KH0015
CT0012	test111	CuocThi	fdsfsfsdf	sdfsdfsdf	tất cả sinh viên	2025-11-22 22:57:00+07	2025-11-29 22:57:00+07	Hội trường C	\N	CaNhan	Approved	10000000.00	\N	KHMT	2025-11-19 15:57:28+07	2025-11-26 02:10:06.08223+07	\N
CT0016	sdfsdf	CuocThi	sd	sf	zdf	2025-12-01 04:05:00+07	2025-12-12 04:05:00+07	s	1	CaNhan	Completed	10000000.00	\N	KHMT	2025-12-07 21:06:37+07	2025-12-07 21:06:37+07	KH0019
CT0017	trtádu	CuocThi	Cuộc thi Nghiên cứu Khoa học và Sáng tạo Trẻ HUIT lần thứ VIII – năm 2026 (HRIC 2026) là sân chơi học thuật thường niên lớn nhất dành riêng cho sinh viên Trường Đại học Công Thương TP.HCM.	ád	d	2025-12-12 01:46:00+07	2025-12-14 01:46:00+07	Hội trường C	\N	CaNhan	Approved	1000000.00	\N	KHMT	2025-12-09 18:48:18+07	2025-12-10 16:04:15+07	KH0020
CT0018	HUIT RESEARCH & INNOVATION COMPETITION 2025	CuocThi	Cuộc thi Nghiên cứu Khoa học và Sáng tạo Trẻ HUIT lần thứ VIII – năm 2026 (HRIC 2026) là sân chơi học thuật thường niên lớn nhất dành riêng cho sinh viên Trường Đại học Công Thương TP.HCM.	Cuộc thi nhằm khuyến khích sinh viên tham gia nghiên cứu khoa học, phát triển tư duy sáng tạo, giải quyết các vấn đề thực tiễn trong lĩnh vực công nghiệp, công nghệ và kinh tế; đồng thời tôn vinh các công trình nghiên cứu xuất sắc và tạo cơ hội giao lưu, học hỏi giữa các khoa, câu lạc bộ học thuật trong toàn trường.\r\nVới 6 lĩnh vực dự thi chính:\r\n1. Kỹ thuật – Công nghệ – Tự động hóa\r\n2. Công nghệ Thông tin & Trí tuệ nhân tạo\r\n3. Công nghệ Thực phẩm & Hóa học\r\n4. Kinh tế – Quản trị – Logistics\r\n5. Khoa học Cơ bản & Môi trường\r\n6. Sáng tạo Khởi nghiệp & Chuyển đổi số\r\nTổng giá trị giải thưởng hơn 250 triệu đồng cùng cúp, giấy khen của Hiệu trưởng và cơ hội công bố trên kỷ yếu khoa học HUIT, tham gia các cuộc thi cấp thành phố/quốc gia.	Sinh viên đại học	2025-12-13 23:05:00+07	2025-12-16 23:05:00+07	Hội trường C	\N	CaNhan	Approved	2000000.00	\N	KHMT	2025-12-10 16:07:17+07	2025-12-10 16:19:16+07	KH0021
CT0019	HUIT TECH & INNOVATION CHALLENGE 2026	CuocThi	HUIT Tech & Innovation Challenge 2026 là cuộc thi thường niên lớn nhất dành cho sinh viên Trường Đại học Công Thương TP.HCM, tập trung giải quyết các vấn đề thực tiễn của doanh nghiệp và xã hội bằng giải pháp công nghệ, kinh doanh và sáng tạo kỹ thuật.	Sinh viên sẽ làm việc theo đội để nhận “đề bài thật” từ các doanh nghiệp đối tác (Vinamilk, Unilever, Bosch, FPT, Shopee, v.v.), đề xuất giải pháp sáng tạo trong 8–10 tuần, sau đó bảo vệ sản phẩm/prototype trước Ban giám khảo là giảng viên HUIT và đại diện doanh nghiệp.	Sinh viên đại học	2026-01-01 23:33:00+07	2026-01-04 23:33:00+07	Hội trường C	2	DoiNhom	Approved	20000000.00	\N	KHMT	2025-12-10 16:35:32+07	2025-12-10 16:35:32+07	KH0022
CT0020	HUIT ACADEMIC OLYMPIC 2026	CuocThi	HUIT Academic Olympic 2026 (HAO 2026) là cuộc thi kiến thức học thuật lớn nhất trong năm dành cho toàn thể sinh viên Trường Đại học Công Thương TP.HCM.	Format hiện đại giống “Đường lên đỉnh Olympia” kết hợp “Ai là triệu phú”, các đội sẽ thi đấu trực tiếp qua 4 vòng: Khởi động – Về đích – Tăng tốc – Vượt chướng ngại vật, tranh tài kiến thức chuyên ngành, kỹ năng mềm, tin tức công nghệ và kiến thức xã hội.	Sinh viên đang học đại học	2025-12-12 23:57:00+07	2025-12-13 23:57:00+07	Hội trường C	\N	CaNhan	Approved	10000000.00	\N	KHMT	2025-12-10 16:58:42+07	2025-12-10 16:59:58+07	KH0023
CT0021	HUIT STARTUP BUILDER 2026 – CUỘC THI XÂY DỰNG DOANH NGHIỆP TỪ CON SỐ 0	CuocThi	HUIT Startup Builder 2026 (HSB 2026) là cuộc thi khởi nghiệp thực chiến lớn nhất dành cho sinh viên Trường Đại học Công Thương TP.HCM.	Khác với các cuộc thi ý tưởng thông thường, HSB yêu cầu các đội thực sự xây dựng và vận hành một doanh nghiệp nhỏ trong 4 tháng: lập công ty, tạo sản phẩm/dịch vụ thật, bán hàng thật, có doanh thu thật và báo cáo tài chính thật.\r\nMục tiêu:\r\n\r\nTrang bị đầy đủ kiến thức và trải nghiệm khởi nghiệp thực tế 100%\r\nGiúp sinh viên sở hữu doanh nghiệp thật ngay khi còn ngồi trên ghế nhà trường\r\nKết nối trực tiếp với quỹ đầu tư, nhà tài trợ và hệ sinh thái khởi nghiệp TP.HCM	Sinh viên đại học	2025-12-09 01:20:00+07	2025-12-14 01:20:00+07	Hội trường C	\N	CaNhan	Approved	20000000.00	\N	KHMT	2025-12-10 18:23:13+07	2025-12-10 18:23:13+07	KH0024
\.


--
-- TOC entry 5150 (class 0 OID 19633)
-- Dependencies: 223
-- Data for Name: dangkycanhan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.dangkycanhan (madangkycanhan, macuocthi, masinhvien, ngaydangky, trangthai, ghichu) FROM stdin;
DKCN001	CT002	2001221872	2024-05-01 10:00:00+07	Registered	Đăng ký tham dự hội thảo
DKCN002	CT002	2001221873	2024-05-01 11:00:00+07	Registered	Đăng ký tham dự hội thảo
DKCN003	CT003	2001221874	2024-04-02 09:00:00+07	Confirmed	Thi cá nhân Olympic
DKCN004	CT007	2001221872	2025-09-10 10:00:00+07	Completed	Tham dự hội thảo
DKCN005	CT007	2001221873	2025-09-10 11:00:00+07	Completed	Tham dự hội thảo
DKCN006	CT007	2001221874	2025-09-11 09:00:00+07	Completed	Tham dự hội thảo
DKCN013	CT006	2001221874	2025-10-25 11:00:00+07	Registered	Đăng ký thi Olympic
DKCN014	CT006	2001221876	2025-10-26 09:00:00+07	Registered	Đăng ký thi Olympic
DKCNHHLGTJ2O	CT006	2001221872	2025-11-21 15:13:26+07	Registered	ssss
DKCNTKQYERJN	CT0013	2001221872	2025-11-29 19:11:41+07	Registered	ssss
DKCNS59EKFVQ	CT0014	2001221872	2025-11-29 19:50:40+07	Registered	d
DKCNU86WCWZQ	CT0015	2001221872	2025-12-02 18:59:35+07	Registered	d
DKCN47WDPY8H	CT0016	2001221872	2025-12-07 21:08:19+07	Registered	sdf
DKCNGH4K9FR3	CT0018	2001221872	2025-12-10 16:29:22+07	Registered	tham gia
DKCNXI9NPNXO	CT0021	2001221872	2025-12-10 18:24:28+07	Registered	sdf
\.


--
-- TOC entry 5151 (class 0 OID 19641)
-- Dependencies: 224
-- Data for Name: dangkydoithi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.dangkydoithi (madangkydoi, macuocthi, madoithi, ngaydangky, trangthai, ghichu) FROM stdin;
DKDT001	CT001	DT001	2024-03-05 09:00:00+07	Registered	Đội nghiên cứu COATI
DKDT002	CT003	DT002	2024-04-02 10:00:00+07	Confirmed	Thi đội Olympic
DKDT003	CT005	DT003	2025-09-20 10:00:00+07	Registered	Đội AI Warriors
DKDT004	CT005	DT004	2025-09-21 14:00:00+07	Registered	Đội Smart City Innovators
DKDT005	CT008	DT005	2025-08-20 10:00:00+07	Completed	Đội WebMasters
DKDTIHM1CKDI	CT0019	DTUYBZHRE4	2025-12-10 16:51:06+07	Registered	super mân
\.


--
-- TOC entry 5152 (class 0 OID 19649)
-- Dependencies: 225
-- Data for Name: dangkyhoatdong; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.dangkyhoatdong (madangkyhoatdong, mahoatdong, masinhvien, ngaydangky, trangthai, diemdanhqr, thoigiandiemdanh) FROM stdin;
DKHD001	HD002	2001221873	2024-06-10 10:00:00+07	Registered	t	2024-06-20 08:30:00+07
DKHD002	HD003	2001221874	2024-05-05 14:00:00+07	Registered	t	2024-05-15 07:30:00+07
DKHD003	HD005	2001221878	2025-10-01 10:00:00+07	Registered	t	2025-10-15 08:15:00+07
DKHD004	HD005	2001221879	2025-10-01 11:00:00+07	Registered	t	2025-10-15 08:20:00+07
DKHD006	HD007	2001221877	2025-09-15 14:00:00+07	Registered	t	2025-09-25 08:30:00+07
DKHDIISZBU61	HD0012	2001221872	2025-11-25 22:46:06+07	Registered	t	2025-11-26 03:18:20+07
DKHDT9EXFZNO	HD0013	2001221872	2025-12-10 17:03:09+07	Registered	t	2025-12-11 00:49:58+07
\.


--
-- TOC entry 5153 (class 0 OID 19655)
-- Dependencies: 226
-- Data for Name: datgiai; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.datgiai (madatgiai, macuocthi, madangkycanhan, madangkydoi, loaidangky, tengiai, giaithuong, diemrenluyen, ngaytrao) FROM stdin;
DG001	CT001	\N	DKDT001	DoiNhom	Giải Nhất	Chứng nhận + 5 triệu đồng	15.00	2024-06-30 15:00:00+07
DG002	CT003	DKCN003	\N	CaNhan	Giải Ba	Chứng nhận + 1 triệu đồng	8.00	2024-05-10 16:00:00+07
DG003	CT003	\N	DKDT002	DoiNhom	Giải Nhất	Chứng nhận + 10 triệu đồng	20.00	2024-05-10 16:00:00+07
DG004	CT008	\N	DKDT005	DoiNhom	Giải Nhì	Chứng nhận + 3 triệu đồng	12.00	2025-09-30 16:00:00+07
DG-TZNOA0R5	CT0014	DKCNS59EKFVQ	\N	CaNhan	Giải Nhất (Hạng 1)	100.000 VNĐ + Giấy khen	15.00	2025-12-01 21:33:53+07
DG-B7Y2WYV9	CT0016	DKCN47WDPY8H	\N	CaNhan	Giải nhất (Hạng 1)	100.000 VNĐ + Giấy khen	15.00	2025-12-07 22:41:35+07
DG-CIEORCKC	CT0016	DKCN47WDPY8H	\N	CaNhan	Giải nhất (Hạng 1)	100.000 VNĐ + Giấy khen	15.00	2025-12-07 22:54:38+07
\.


--
-- TOC entry 5154 (class 0 OID 19662)
-- Dependencies: 227
-- Data for Name: dethi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.dethi (madethi, tendethi, macuocthi, loaidethi, filedethi, thoigianlambai, diemtoida, ngaytao, nguoitao, trangthai) FROM stdin;
DE001	Đề tài nghiên cứu COATI-FCM	CT001	Nghiên cứu	\N	120	100.00	2024-02-25 10:00:00+07	GV001	Published
DE002	Đề thi Olympic Tin học	CT003	Lập trình	\N	180	100.00	2024-03-20 10:00:00+07	GV002	Published
DE003	Thiết kế Website Thương mại điện tử	CT008	ThucHanh	\N	480	100.00	2025-08-15 10:00:00+07	GV002	Published
DE007	Đề án Hackathon AI	CT005	DeAn	\N	2880	100.00	2025-09-15 10:00:00+07	GV001	Published
DE006	Đề thi Olympic Tin học	CT006	ThucHanh	dethi/1763744913_CLD_MucChiTiet.pdf	240	100.00	2025-11-10 10:00:00+07	GV002	Published
DE005	Đề thi Chung kết Database Challenge	CT004	ThucHanh	dethi/1763750481_S_________l___p_Cu___i.docx	60	100.00	2025-11-25 10:00:00+07	GV002	Published
DE004	Đề thi Sơ khảo Database Challenge	CT004	VietBao	dethi/1763751474_CLD_MucChiTiet.pdf	60	100.00	2025-11-25 10:00:00+07	GV002	Published
DT0001	ssss	CT0013	LyThuyet	dethi/1764443540_quyet-toan-QT0007.pdf	60	10.00	2025-11-29 19:12:20+07	GV001	Published
DT0002	trrrr	CT0014	LyThuyet	dethi/1764443540_quyet-toan-QT0007.pdf	60	10.00	2025-11-29 19:36:54+07	GV001	Published
DT0003	đsd	CT0015	ThucHanh	dethi/1764708028_quyet-toan-QT0007.pdf	60	10.00	2025-12-02 19:03:01+07	GV002	Published
DT0004	trrrrzdfsfsfà	CT0016	ThucHanh	dethi/1765141752_test.pdf	60	10.00	2025-12-07 21:09:12+07	GV001	Published
DT0005	Đề thi HUIT RESEARCH & INNOVATION COMPETITION 2025	CT0018	ThucHanh	dethi/1765384102_test.pdf	60	10.00	2025-12-10 16:28:22+07	GV002	Published
DT0006	Dề thi HUIT TECH	CT0019	ThucHanh	dethi/1765385261_quyet-toan-QT0007__1_.pdf	60	10.00	2025-12-10 16:44:37+07	GV001	Published
DT0007	Dề thi HUIT STARTUP BUILDER 2026 – CUỘC THI XÂY DỰNG DOANH NGHIỆP TỪ CON SỐ 0	CT0021	ThucHanh	dethi/1765391199_quyet-toan-QT0007__2_.pdf	60	10.00	2025-12-10 18:26:39+07	GV001	Published
\.


--
-- TOC entry 5155 (class 0 OID 19669)
-- Dependencies: 228
-- Data for Name: diemdanhlichhoc; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.diemdanhlichhoc (madiemdanh, malichhoc, masinhvien, ngayhoc, trangthai, ghichu, thoigiandiemdanh) FROM stdin;
DDLH001	LH001	2001221872	2024-01-08	CoMat	Đến đúng giờ	2025-11-17 02:29:37.895936+07
DDLH002	LH001	2001221874	2024-01-08	CoMat	Đến đúng giờ	2025-11-17 02:29:37.895936+07
DDLH003	LH002	2001221872	2024-01-09	DiTre	Đến muộn 10 phút	2025-11-17 02:29:37.895936+07
DDLH004	LH003	2001221873	2024-01-10	CoMat	Đến đúng giờ	2025-11-17 02:29:37.895936+07
DDLH005	LH001	2001221872	2024-01-15	VangCoPhep	Ốm có đơn xin phép	2025-11-17 02:29:37.895936+07
DDLH006	LH004	2001221873	2024-01-11	CoMat	Tham gia tích cực	2025-11-17 02:29:37.895936+07
\.


--
-- TOC entry 5156 (class 0 OID 19676)
-- Dependencies: 229
-- Data for Name: diemdanhqr; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.diemdanhqr (madiemdanh, mahoatdong, macuocthi, masinhvien, maqr, thoigiandiemdanh, vitri) FROM stdin;
DD001	HD002	CT001	2001221873	QR_HD002_2001221873	2024-06-20 08:30:00+07	Hội trường B - UIT
DD002	HD003	CT002	2001221874	QR_HD003_2001221874	2024-05-15 07:30:00+07	Hội trường A - UIT
DD003	\N	CT003	2001221872	QR_CT003_2001221872	2024-04-25 08:00:00+07	Phòng máy B - UIT
DD004	HD005	CT005	2001221878	QR_HD005_2001221878	2025-10-15 08:15:00+07	HUIT - B Building
DD005	HD005	CT005	2001221879	QR_HD005_2001221879	2025-10-15 08:20:00+07	HUIT - B Building
DD006	HD007	CT007	2001221877	QR_HD007_2001221877	2025-09-25 08:30:00+07	Hội trường C - HUIT
DDF9DRHW3F	HD0012	CT0012	2001221872	GOOGLE-FORM	2025-11-26 03:18:20+07	Import từ Google Form
\.


--
-- TOC entry 5157 (class 0 OID 19682)
-- Dependencies: 230
-- Data for Name: diemrenluyen; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.diemrenluyen (madiemrl, masinhvien, macuocthi, mahoatdong, loaihoatdong, diem, mota, ngaycong, magangiai) FROM stdin;
RL001	2001221872	CT001	\N	DatGiai	15.00	Giải Nhất cuộc thi COATI	2024-06-30 16:00:00+07	\N
RL002	2001221872	CT001	\N	DuThi	10.00	Tham gia cuộc thi	2024-06-30 16:00:00+07	\N
RL003	2001221873	\N	HD002	HoTro	3.00	Cổ vũ cuộc thi	2024-06-20 12:00:00+07	\N
RL004	2001221874	CT003	\N	DatGiai	8.00	Giải Ba Olympic Tin học	2024-05-10 16:00:00+07	\N
RL005	2001221873	CT003	\N	DatGiai	20.00	Giải Nhất Olympic Tin học (đội)	2024-05-10 16:00:00+07	\N
RL006	2001221874	\N	HD003	HoTro	5.00	Tổ chức hội thảo	2024-05-15 18:00:00+07	\N
RL010	2001221878	\N	HD005	HoTro	6.00	Hỗ trợ kỹ thuật Hackathon AI 3 ngày	2025-10-17 18:00:00+07	\N
RL011	2001221879	\N	HD005	HoTro	6.00	Hỗ trợ kỹ thuật Hackathon AI 3 ngày	2025-10-17 18:00:00+07	\N
RL012	2001221877	\N	HD007	HoTro	3.00	Quay phim Hội thảo ATTT	2025-09-25 17:00:00+07	\N
RL013	2001221872	CT007	\N	DuThi	2.00	Tham dự Hội thảo ATTT	2025-09-25 17:00:00+07	\N
RL014	2001221873	CT007	\N	DuThi	2.00	Tham dự Hội thảo ATTT	2025-09-25 17:00:00+07	\N
RL015	2001221874	CT007	\N	DuThi	2.00	Tham dự Hội thảo ATTT	2025-09-25 17:00:00+07	\N
DRL17641108044345	2001221872	CT0012	HD0012	HoTro	2.00	Điểm danh hoạt động: tess	2025-11-25 22:46:44+07	\N
DRL-ODSUYP7J	2001221872	CT0014	\N	DatGiai	15.00	Đạt giải: Giải Nhất (Hạng 1) - 100.000 VNĐ + Giấy khen	2025-12-01 21:33:53+07	GG-FOFIAYZQ
DRL-E6CPEZQE	2001221872	CT0016	\N	DatGiai	15.00	Đạt giải: Giải nhất (Hạng 1) - 100.000 VNĐ + Giấy khen	2025-12-07 22:41:35+07	\N
DRL-SDPGVOPP	2001221872	CT0016	\N	DatGiai	15.00	Đạt giải: Giải nhất (Hạng 1) - 100.000 VNĐ + Giấy khen	2025-12-07 22:54:38+07	GG-DDOZ5LQS
\.


--
-- TOC entry 5158 (class 0 OID 19689)
-- Dependencies: 231
-- Data for Name: doithi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.doithi (madoithi, tendoithi, macuocthi, matruongdoi, sothanhvien, ngaydangky, trangthai) FROM stdin;
DT001	Team COATI Research	CT001	2001221872	2	2024-03-05 09:00:00+07	Active
DT002	Team Algorithm Masters	CT003	2001221873	1	2024-04-02 10:00:00+07	Active
DT003	AI Warriors	CT005	2001221874	2	2025-09-20 10:00:00+07	Active
DT004	Smart City Innovators	CT005	2001221876	2	2025-09-21 14:00:00+07	Active
DT005	WebMasters	CT008	2001221878	1	2025-08-20 10:00:00+07	Active
DTUYBZHRE4	Super man	CT0019	2001221872	2	2025-12-10 16:51:06+07	Active
\.


--
-- TOC entry 5176 (class 0 OID 20191)
-- Dependencies: 249
-- Data for Name: gangiaithuong; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.gangiaithuong (magangiai, macocau, madangkycanhan, madangkydoi, loaidangky, ladonghang, xephangthucte, trangthai, nguoiduyet, ngayduyet, ghichu, nguoigan, ngaygan) FROM stdin;
GG-FOFIAYZQ	COCAU-3HW6AA5U	DKCNS59EKFVQ	\N	CaNhan	f	1	Approved	GV002	2025-12-01 21:33:53+07	Gán tự động theo xếp hạng	GV002	2025-12-01 21:33:53+07
GG-DDOZ5LQS	COCAU-96AGG4VM	DKCN47WDPY8H	\N	CaNhan	f	1	Approved	GV002	2025-12-07 22:54:38+07	df	GV002	2025-12-07 22:54:38+07
\.


--
-- TOC entry 5159 (class 0 OID 19695)
-- Dependencies: 232
-- Data for Name: giangvien; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.giangvien (magiangvien, manguoidung, mabomon, chucvu, hocvi, chuyenmon, is_admin) FROM stdin;
GV002	ND004	KHMT	Phó trưởng khoa	Tiến sĩ	Công nghệ phần mềm, Hệ thống thông tin	f
GV003	ND007	KHMT	Giảng viên	Thạc sĩ	Lập trình, Cơ sở dữ liệu	f
GV001	ND002	KHMT	Giảng viên	Thạc sĩ	Khoa học dữ liệu, Trí tuệ nhân tạo	f
ADMIN	ND000014	\N	\N	\N	\N	t
\.


--
-- TOC entry 5160 (class 0 OID 19700)
-- Dependencies: 233
-- Data for Name: hoatdonghotro; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.hoatdonghotro (mahoatdong, tenhoatdong, macuocthi, loaihoatdong, diemrenluyen, thoigianbatdau, thoigianketthuc, diadiem, mota, soluong) FROM stdin;
HD001	Hỗ trợ kỹ thuật cuộc thi	CT001	HoTroKyThuat	5.00	2024-03-01 08:00:00+07	2024-06-30 17:00:00+07	UIT	Hỗ trợ kỹ thuật cho các đội thi	20
HD002	Cổ vũ cuộc thi	CT001	CoVu	3.00	2024-06-20 08:00:00+07	2024-06-20 12:00:00+07	Hội trường B	Cổ vũ các đội thi trong ngày thi	20
HD003	Tổ chức hội thảo	CT002	ToChuc	5.00	2024-05-15 07:00:00+07	2024-05-15 18:00:00+07	Hội trường A	Hỗ trợ tổ chức hội thảo	20
HD004	Hỗ trợ tổ chức Database Challenge	CT004	ToChuc	4.00	2025-12-07 07:00:00+07	2025-12-07 16:30:00+07	HUIT	Hỗ trợ tổ chức cuộc thi, điểm danh, hướng dẫn thí sinh	20
HD005	Hỗ trợ kỹ thuật Hackathon AI	CT005	HoTroKyThuat	6.00	2025-10-15 08:00:00+07	2025-10-17 18:00:00+07	HUIT	Hỗ trợ kỹ thuật 3 ngày thi liên tục	20
HD007	Quay phim Hội thảo ATTT	CT007	ToChuc	3.00	2025-09-25 08:00:00+07	2025-09-25 17:00:00+07	Hội trường C	Quay phim và chụp ảnh hội thảo	20
HD008	Cổ vũ Game Development Challenge 2025	CT010	CoVu	3.00	2025-11-30 13:00:00+07	2025-11-30 17:00:00+07	B Building - HUIT	Cổ vũ, biểu diễn flashmob, tạo bầu không khí sôi động cho vòng chung kết và buổi demo game	20
HD009	Hỗ trợ kỹ thuật Game Development Challenge 2025	CT010	HoTroKyThuat	3.00	2025-11-30 13:00:00+07	2025-11-30 17:00:00+07	B Building - HUIT	Hỗ trợ kỹ thuật, setup thiết bị, xử lý sự cố mạng, âm thanh, ánh sáng cho vòng chung kết và buổi demo game	20
HD011	Hỗ trợ kỹ thuật Olympic Tin học Sinh viên 2025	CT006	HoTroKyThuat	3.00	2025-11-22 13:00:00+07	2025-11-30 17:00:00+07	B Building - HUIT	Hỗ trợ kỹ thuật, setup thiết bị, xử lý sự cố mạng, âm thanh, ánh sáng cho vòng chung kết và buổi demo game	20
HD010	Cổ vũ Game Olympic Tin học Sinh viên 2025	CT006	CoVu	3.00	2025-11-22 13:00:00+07	2025-11-30 17:00:00+07	B Building - HUIT	Cổ vũ, biểu diễn flashmob, tạo bầu không khí sôi động cho vòng chung kết và buổi demo game	20
HD0012	tess	CT0012	CoVu	2.00	2025-11-22 03:24:00+07	2025-11-29 03:24:00+07	C	ff	50
HD0013	Cổ vũ HUIT ACADEMIC OLYMPIC 2026	CT0020	CoVu	2.00	2025-12-10 00:01:00+07	2025-12-12 00:01:00+07	Hội trường C	Cổ vũ	48
\.


--
-- TOC entry 5161 (class 0 OID 19707)
-- Dependencies: 234
-- Data for Name: kehoachcuocthi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kehoachcuocthi (makehoach, namhoc, hocky, trangthaiduyet, ngaynopkehoach, ngayduyet, nguoiduyet, ghichu, tencuocthi, loaicuocthi, mota, mucdich, doituongthamgia, thoigianbatdau, thoigianketthuc, diadiem, soluongthanhvien, hinhthucthamgia, dutrukinhphi, mabomon, nguoinop) FROM stdin;
KH001	2023-2024	HK2	Approved	2024-02-01 10:00:00+07	2024-02-15 14:00:00+07	GV002	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH002	2023-2024	HK2	Approved	2024-04-01 10:00:00+07	2024-04-10 14:00:00+07	GV001	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH003	2023-2024	HK2	Approved	2024-03-01 10:00:00+07	2024-03-10 14:00:00+07	GV002	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH004	2025-2026	HK1	Approved	2025-11-05 10:00:00+07	2025-11-10 14:00:00+07	GV002	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH005	2025-2026	HK1	Approved	2025-09-01 10:00:00+07	2025-09-10 14:00:00+07	GV001	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH006	2025-2026	HK1	Approved	2025-10-01 10:00:00+07	2025-10-05 14:00:00+07	GV002	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH007	2025-2026	HK1	Approved	2025-08-15 10:00:00+07	2025-08-20 14:00:00+07	GV002	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH008	2025-2026	HK1	Approved	2025-07-20 10:00:00+07	2025-07-25 14:00:00+07	GV002	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH009	2024-2025	HK3	Approved	2025-06-01 10:00:00+07	2025-06-10 14:00:00+07	GV002	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH010	2025-2026	HK1	Approved	2025-09-10 10:00:00+07	2025-09-15 14:00:00+07	GV002	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH011	2025-2026	HK1	Approved	2025-08-01 10:00:00+07	2025-08-10 14:00:00+07	GV001	Kế hoạch đã được phê duyệt	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH0012	2024-2025	1	Approved	2025-11-25 18:23:58+07	2025-11-26 02:10:06.08223+07	GV002	trrr	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH0013	2024-2025	2	Approved	2025-11-26 14:59:13+07	2025-11-26 14:59:52+07	GV002	ssss	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH0014	2024-2025	1	Approved	2025-11-28 09:30:44+07	2025-11-28 09:31:05+07	GV002	đ	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
KH0022	2024-2025	3	Approved	2025-12-10 16:34:22+07	2025-12-10 16:35:19+07	GV002	\N	HUIT TECH & INNOVATION CHALLENGE 2026	CuocThi	HUIT Tech & Innovation Challenge 2026 là cuộc thi thường niên lớn nhất dành cho sinh viên Trường Đại học Công Thương TP.HCM, tập trung giải quyết các vấn đề thực tiễn của doanh nghiệp và xã hội bằng giải pháp công nghệ, kinh doanh và sáng tạo kỹ thuật.	Sinh viên sẽ làm việc theo đội để nhận “đề bài thật” từ các doanh nghiệp đối tác (Vinamilk, Unilever, Bosch, FPT, Shopee, v.v.), đề xuất giải pháp sáng tạo trong 8–10 tuần, sau đó bảo vệ sản phẩm/prototype trước Ban giám khảo là giảng viên HUIT và đại diện doanh nghiệp.	Sinh viên đại học	2026-01-01 23:33:00+07	2026-01-04 23:33:00+07	Hội trường C	2	DoiNhom	20000000.00	KHMT	GV001
KH0015	2024-2025	1	Approved	2025-11-28 18:11:54+07	2025-11-28 18:23:17+07	GV002	\N	edg	CuocThi	s	s	d	2025-11-30 01:07:00+07	2025-12-07 01:07:00+07	s	100	CaNhan	10000000.00	KHMT	GV001
KH0016	2024-2025	2	Approved	2025-11-28 19:57:35+07	2025-11-28 19:58:01+07	GV002	\N	ssss	CuocThi	s	s	s	2025-11-30 02:57:00+07	2025-12-04 02:57:00+07	s	1	CaNhan	999998000.00	KHMT	GV002
KH0017	2024-2025	2	Approved	2025-12-02 18:57:40+07	2025-12-02 18:57:56+07	GV002	\N	sádâd	CuocThi	đ	d	d	2025-12-05 01:57:00+07	2025-12-08 01:57:00+07	d	\N	CaNhan	100000.00	KHMT	GV002
KH0019	2024-2025	2	Approved	2025-12-07 21:06:11+07	2025-12-07 21:06:31+07	GV002	\N	sdfsdf	CuocThi	sd	sf	zdf	2025-12-08 04:05:00+07	2025-12-11 04:05:00+07	s	1	CaNhan	10000000.00	KHMT	GV001
KH0020	2024-2025	2	Approved	2025-12-09 18:47:03+07	2025-12-09 18:47:55+07	GV002	\N	trtádu	CuocThi	áđsa	ád	d	2025-12-12 01:46:00+07	2025-12-14 01:46:00+07	s	\N	CaNhan	1000000.00	KHMT	GV001
KH0021	2024-2025	3	Approved	2025-12-10 16:06:48+07	2025-12-10 16:07:09+07	GV002	\N	HUIT RESEARCH & INNOVATION COMPETITION 2025	CuocThi	Cuộc thi Nghiên cứu Khoa học và Sáng tạo Trẻ HUIT lần thứ VIII – năm 2026 (HRIC 2026) là sân chơi học thuật thường niên lớn nhất dành riêng cho sinh viên Trường Đại học Công Thương TP.HCM.	Cuộc thi nhằm khuyến khích sinh viên tham gia nghiên cứu khoa học, phát triển tư duy sáng tạo, giải quyết các vấn đề thực tiễn trong lĩnh vực công nghiệp, công nghệ và kinh tế; đồng thời tôn vinh các công trình nghiên cứu xuất sắc và tạo cơ hội giao lưu, học hỏi giữa các khoa, câu lạc bộ học thuật trong toàn trường.\r\nVới 6 lĩnh vực dự thi chính:\r\n1. Kỹ thuật – Công nghệ – Tự động hóa\r\n2. Công nghệ Thông tin & Trí tuệ nhân tạo\r\n3. Công nghệ Thực phẩm & Hóa học\r\n4. Kinh tế – Quản trị – Logistics\r\n5. Khoa học Cơ bản & Môi trường\r\n6. Sáng tạo Khởi nghiệp & Chuyển đổi số\r\nTổng giá trị giải thưởng hơn 250 triệu đồng cùng cúp, giấy khen của Hiệu trưởng và cơ hội công bố trên kỷ yếu khoa học HUIT, tham gia các cuộc thi cấp thành phố/quốc gia.	Sinh viên đại học	2025-12-13 23:05:00+07	2025-12-16 23:05:00+07	Hội trường C	\N	CaNhan	2000000.00	KHMT	GV001
KH0023	2024-2025	2	Approved	2025-12-10 16:58:27+07	2025-12-10 16:58:36+07	GV002	\N	HUIT ACADEMIC OLYMPIC 2026	CuocThi	HUIT Academic Olympic 2026 (HAO 2026) là cuộc thi kiến thức học thuật lớn nhất trong năm dành cho toàn thể sinh viên Trường Đại học Công Thương TP.HCM.	Format hiện đại giống “Đường lên đỉnh Olympia” kết hợp “Ai là triệu phú”, các đội sẽ thi đấu trực tiếp qua 4 vòng: Khởi động – Về đích – Tăng tốc – Vượt chướng ngại vật, tranh tài kiến thức chuyên ngành, kỹ năng mềm, tin tức công nghệ và kiến thức xã hội.	Sinh viên đang học đại học	2025-12-12 23:57:00+07	2025-12-13 23:57:00+07	Hội trường C	\N	CaNhan	10000000.00	KHMT	GV002
KH0024	2024-2025	2	Approved	2025-12-10 18:21:01+07	2025-12-10 18:22:51+07	GV002	\N	HUIT STARTUP BUILDER 2026 – CUỘC THI XÂY DỰNG DOANH NGHIỆP TỪ CON SỐ 0	CuocThi	HUIT Startup Builder 2026 (HSB 2026) là cuộc thi khởi nghiệp thực chiến lớn nhất dành cho sinh viên Trường Đại học Công Thương TP.HCM.	Khác với các cuộc thi ý tưởng thông thường, HSB yêu cầu các đội thực sự xây dựng và vận hành một doanh nghiệp nhỏ trong 4 tháng: lập công ty, tạo sản phẩm/dịch vụ thật, bán hàng thật, có doanh thu thật và báo cáo tài chính thật.\r\nMục tiêu:\r\n\r\nTrang bị đầy đủ kiến thức và trải nghiệm khởi nghiệp thực tế 100%\r\nGiúp sinh viên sở hữu doanh nghiệp thật ngay khi còn ngồi trên ghế nhà trường\r\nKết nối trực tiếp với quỹ đầu tư, nhà tài trợ và hệ sinh thái khởi nghiệp TP.HCM	Sinh viên đại học	2025-12-09 01:20:00+07	2025-12-14 01:20:00+07	Hội trường C	\N	CaNhan	20000000.00	KHMT	GV001
\.


--
-- TOC entry 5162 (class 0 OID 19714)
-- Dependencies: 235
-- Data for Name: ketquathi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.ketquathi (maketqua, mabaithi, diem, xephang, giaithuong, nhanxet, ngaychamdiem, nguoichamdiem) FROM stdin;
KQ001	BT001	85.00	1	Giải Nhất	Nghiên cứu sâu sắc, ứng dụng thực tế tốt	2024-06-25 15:00:00+07	GV001
KQ002	BT002	75.00	3	Giải Ba	Code tốt, thuật toán hiệu quả	2024-05-05 14:00:00+07	GV002
KQ003	BT003	90.00	1	Giải Nhất	Xuất sắc, giải pháp tối ưu	2024-05-05 14:30:00+07	GV002
KQ004	BT004	90.00	2	Giải Nhì	Thiết kế đẹp, tính năng đầy đủ, trải nghiệm người dùng tốt	2025-09-30 15:00:00+07	GV002
KQ000005	BT17637492583959	8.00	1	Giải Nhất	ssss	2025-11-26 13:22:32+07	GV002
KQ000006	BT17644437158120	8.00	1	Giải Nhất	đss	2025-11-29 19:31:16+07	GV001
KQ-692DE3D5DFD18	BT17644459653120	8.00	1	Giải Nhất	ssdsd1	2025-12-01 19:09:00+07	GV001
KQ000007	BT17651420572391	10.00	1	\N	quá là hay	2025-12-07 22:54:15+07	GV001
\.


--
-- TOC entry 5163 (class 0 OID 19719)
-- Dependencies: 236
-- Data for Name: lichhoc; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lichhoc (malichhoc, mamonhoc, malop, magiangvien, thu, tietbatdau, tietketthuc, phonghoc, ngaybatdau, ngayketthuc, ghichu) FROM stdin;
LH001	IT001	13DHTH02	GV003	Thu2	1	3	A1.501	2024-01-08	2024-05-31	Lịch học HK2
LH002	IT002	13DHTH02	GV002	Thu3	4	6	B2.301	2024-01-09	2024-05-31	Lịch học HK2
LH003	DS001	13KHDL01	GV001	Thu4	7	9	C3.201	2024-01-10	2024-05-31	Lịch học HK2
LH004	DS002	13KHDL01	GV001	Thu5	1	3	C3.202	2024-01-11	2024-05-31	Lịch học HK2
LH005	IT003	14DHTH01	GV003	Thu6	4	6	A2.401	2024-01-12	2024-05-31	Lịch học HK2
\.


--
-- TOC entry 5164 (class 0 OID 19728)
-- Dependencies: 237
-- Data for Name: lop; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.lop (malop, tenlop, nienkhoa, soluongsinhvien, magiangvienchunhiem) FROM stdin;
14CNPM01	Lớp 14CNPM01	2023-2027	1	\N
13DHTH02	Lớp 13DHTH02	2022-2026	4	GV002
13KHDL01	Lớp 13KHDL01	2022-2026	2	GV001
14DHTH01	Lớp 14DHTH01	2023-2027	2	GV003
\.


--
-- TOC entry 5174 (class 0 OID 20143)
-- Dependencies: 247
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2025_11_26_195543_add_is_admin_to_giangvien_table	1
2	2025_12_01_230503_create_password_reset_otps_table	2
3	2025_12_09_172234_add_anhdaidien_to_nguoidung_table	3
\.


--
-- TOC entry 5165 (class 0 OID 19732)
-- Dependencies: 238
-- Data for Name: monhoc; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.monhoc (mamonhoc, tenmonhoc, sotinchi, mabomon, mota) FROM stdin;
IT001	Nhập môn Lập trình	4	KHMT	Môn học cơ bản về lập trình
IT002	Cơ sở dữ liệu	4	KHMT	Thiết kế và quản trị cơ sở dữ liệu
IT003	Cấu trúc dữ liệu và giải thuật	4	KHMT	Các cấu trúc dữ liệu và thuật toán cơ bản
DS001	Khoa học dữ liệu	3	KHDL	Giới thiệu về khoa học dữ liệu
DS002	Học máy	4	KHDL	Machine Learning cơ bản và nâng cao
\.


--
-- TOC entry 5166 (class 0 OID 19737)
-- Dependencies: 239
-- Data for Name: nguoidung; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.nguoidung (manguoidung, tendangnhap, matkhau, hoten, email, sodienthoai, vaitro, trangthai, ngaytao, ngaycapnhat, anhdaidien) FROM stdin;
ND008	2001221870	$2a$10$abcdefghijklmnopqrstuv	Trần Minh Tuấn	tuantm@gm.uit.edu.vn	0989012345	SinhVien	Active	2025-11-17 02:40:38.931305+07	2025-11-17 02:40:38.931305+07	\N
ND009	2001221875	$2a$10$abcdefghijklmnopqrstuv	Võ Thị Lan	lanvt@gm.uit.edu.vn	0990123456	SinhVien	Active	2025-11-17 02:40:38.931305+07	2025-11-17 02:40:38.931305+07	\N
ND010	2001221876	$2a$10$abcdefghijklmnopqrstuv	Nguyễn Văn Hùng	hungnv@gm.uit.edu.vn	0901234568	SinhVien	Active	2025-11-17 02:40:38.931305+07	2025-11-17 02:40:38.931305+07	\N
ND012	2001221878	$2a$10$abcdefghijklmnopqrstuv	Phạm Văn Đức	ducpv@gm.uit.edu.vn	0923456780	SinhVien	Active	2025-11-17 02:40:38.931305+07	2025-11-17 02:40:38.931305+07	\N
ND013	2001221879	$2a$10$abcdefghijklmnopqrstuv	Hoàng Thị Thu	thuht@gm.uit.edu.vn	0934567891	SinhVien	Active	2025-11-17 02:40:38.931305+07	2025-11-17 02:40:38.931305+07	\N
ND005	2001221873	$2y$12$yXbhY0gln8W6eO2Cj7lJieqE6wmQWmC5YFTCXbZfYXSeHMxzjlnCC	Nguyễn Thị Ánh	anhnt@gm.uit.edu.vn	0945678901	SinhVien	Active	2025-11-17 02:29:37.895936+07	2025-11-17 02:29:37.895936+07	\N
ND002	GV001	$2y$12$yXbhY0gln8W6eO2Cj7lJieqE6wmQWmC5YFTCXbZfYXSeHMxzjlnCC	Nguyễn Thị Thúy Trang	trangntt@uit.edu.vn	0912345678	GiangVien	Active	2025-11-17 02:29:37.895936+07	2025-11-17 02:29:37.895936+07	\N
ND007	GV003	$2y$12$yXbhY0gln8W6eO2Cj7lJieqE6wmQWmC5YFTCXbZfYXSeHMxzjlnCC	Lê Thị Hương	huonglt@uit.edu.vn	0967890123	GiangVien	Active	2025-11-17 02:29:37.895936+07	2025-11-17 02:29:37.895936+07	\N
ND001	admin1	$2y$12$yXbhY0gln8W6eO2Cj7lJieqE6wmQWmC5YFTCXbZfYXSeHMxzjlnCC	Nguyễn Văn Admin	admin@uit.edu.vn	0901234567	Admin	Active	2025-11-17 02:29:37.895936+07	2025-11-17 02:29:37.895936+07	\N
ND000014	admin	$2y$12$iUSg7iUljnzhVqPh/9jOuuQlpB.2zUj7xmKx/cuOFXTHTXEag7IRi	Administrator	admin@huit.edu.vn	0123456789	GiangVien	Active	2025-11-26 20:11:41+07	2025-11-26 20:11:41+07	\N
ND006	2001221874	$2a$10$abcdefghijklmnopqrstuv	Trần Văn Bình	binhtv@gm.uit.edu.vn	0956789012	SinhVien	Active	2025-11-17 02:29:37.895936+07	2025-12-07 19:30:45+07	\N
ND011	2001221877	$2a$10$abcdefghijklmnopqrstuv	Lê Thị Mai	mailt@gm.uit.edu.vn	0912345679	SinhVien	Active	2025-11-17 02:40:38.931305+07	2025-12-07 19:34:54+07	\N
ND003	2001221872	$2y$12$UGQhg7bn0VuhM2AICrv7nO/Ig2GaNEIncTskDAxjPbNMHxsPzoAau	Lê Trung Kiên	trungkiena1206052004@gmail.com	0923456789	SinhVien	Active	2025-11-17 02:29:37.895936+07	2025-12-09 17:36:56+07	avatars/04VClUiO2ooXIIrLedPHIxb7thcGc0BnZRsxEacU.png
ND004	GV002	$2y$12$yXbhY0gln8W6eO2Cj7lJieqE6wmQWmC5YFTCXbZfYXSeHMxzjlnCC	Nguyễn Ngọc Thành	thanhnn@uit.edu.vn	0934567890	GiangVien	Active	2025-11-17 02:29:37.895936+07	2025-12-09 17:45:49+07	avatars/CNGWFuuXCJaQBiP2xcMTI0QXB7BFtVUyAzcYyROa.gif
\.


--
-- TOC entry 5178 (class 0 OID 20239)
-- Dependencies: 251
-- Data for Name: password_reset_otps; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_otps (id, email, otp, created_at, expires_at, is_used) FROM stdin;
14	trungkiena1206052004@gmail.com	741594	\N	2025-12-02 21:15:56	t
\.


--
-- TOC entry 5167 (class 0 OID 19746)
-- Dependencies: 240
-- Data for Name: phanconggiangvien; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.phanconggiangvien (maphancong, magiangvien, macongviec, maban, vaitro, ngayphancong) FROM stdin;
PC003	GV002	CV003	BAN005	TruongBan	2025-11-01 10:00:00+07
PC004	GV001	CV003	BAN005	ThanhVien	2025-11-01 10:00:00+07
PC005	GV002	CV004	BAN006	TruongBan	2025-11-20 10:00:00+07
PC006	GV002	CV005	BAN007	TruongBan	2025-11-20 10:00:00+07
PC007	GV001	CV006	BAN009	TruongBan	2025-10-01 10:00:00+07
PC008	GV001	CV007	BAN008	TruongBan	2025-10-01 10:00:00+07
PC009	GV002	CV007	BAN008	ThanhVien	2025-10-01 10:00:00+07
PC001	GV002	CV001	BAN001	TruongBan	2024-02-15 10:00:00+07
PC002	GV002	CV002	BAN002	ThanhVien	2024-02-15 10:00:00+07
PC0010	GV001	CV004	BAN007	Thành Viên	2025-11-26 00:00:00+07
PC0011	GV001	CV018	BAN010	Thành viên	2025-11-29 00:00:00+07
PC0012	GV001	CV019	BAN011	Thành viên	2025-11-29 00:00:00+07
PC0013	GV001	CV021	BAN014	Thành viên	2025-12-07 00:00:00+07
PC0014	GV002	CV022	BAN015	Trưởng ban	2025-12-10 00:00:00+07
PC0015	GV003	CV023	BAN016	Thành viên	2025-12-10 00:00:00+07
PC0016	GV001	CV024	BAN017	Thành viên	2025-12-10 00:00:00+07
PC0017	GV002	CV025	BAN018	Trưởng ban	2025-12-10 00:00:00+07
PC0018	GV003	CV026	BAN018	Thành viên	2025-12-10 00:00:00+07
PC0019	GV001	CV027	BAN020	Thành viên	2025-12-10 00:00:00+07
PC0020	GV001	CV028	BAN021	Thành viên	2025-12-10 00:00:00+07
PC0021	GV001	CV028	BAN021	Thành viên	2025-12-10 00:00:00+07
\.


--
-- TOC entry 5168 (class 0 OID 19750)
-- Dependencies: 241
-- Data for Name: quyettoan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.quyettoan (maquyettoan, macuocthi, tongdutru, tongthucte, chenhlech, ngayquyettoan, nguoilap, nguoiduyet, trangthai, filequyettoan, ghichu) FROM stdin;
QT001	CT001	15000000.00	14500000.00	500000.00	2024-07-10	GV001	GV002	Approved	\N	Quyết toán cuộc thi COATI
QT002	CT002	20000000.00	19000000.00	1000000.00	2024-05-20	GV001	GV002	Approved	\N	Quyết toán hội thảo
QT004	CT007	22000000.00	21700000.00	300000.00	2025-10-05	GV002	GV002	Approved	\N	Quyết toán Hội thảo ATTT
QT005	CT008	16000000.00	15300000.00	700000.00	2025-10-10	GV002	GV002	Approved	\N	Quyết toán Web Design Contest
QT003	CT003	30000000.00	28500000.00	1500000.00	2024-05-15	GV002	GV002	Approved	\N	Đang soát xét quyết toán
QT0006	CT006	18000000.00	18000000.00	0.00	2025-11-27	GV001	\N	Pending	quyettoan/1764275499_1763661894_2._CLD_MucChiTiet.pdf	ss
QT0007	CT0016	200000.00	190000.00	10000.00	2025-12-07	GV001	\N	Pending	quyettoan/1765149392_quyet-toan-QT0007 (1).pdf	đ
\.


--
-- TOC entry 5169 (class 0 OID 19756)
-- Dependencies: 242
-- Data for Name: sinhvien; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sinhvien (masinhvien, manguoidung, malop, namnhaphoc, diemrenluyen, trangthai) FROM stdin;
2001221873	ND005	13KHDL01	2022	95.00	Active
2001221874	ND006	13DHTH02	2022	85.00	Active
2001221870	ND008	13DHTH02	2022	70.00	Active
2001221875	ND009	14DHTH01	2023	70.00	Active
2001221876	ND010	13KHDL01	2022	70.00	Active
2001221877	ND011	14CNPM01	2023	73.00	Active
2001221878	ND012	13DHTH02	2022	76.00	Active
2001221879	ND013	14DHTH01	2023	76.00	Active
2001221872	ND003	13DHTH02	2022	101.00	Active
\.


--
-- TOC entry 5170 (class 0 OID 19761)
-- Dependencies: 243
-- Data for Name: thanhviendoithi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.thanhviendoithi (mathanhvien, madoithi, masinhvien, vaitro, ngaythamgia) FROM stdin;
TV001	DT001	2001221872	TruongDoi	2024-03-05 09:00:00+07
TV002	DT001	2001221874	ThanhVien	2024-03-05 09:30:00+07
TV003	DT002	2001221873	TruongDoi	2024-04-02 10:00:00+07
TV004	DT003	2001221874	TruongDoi	2025-09-20 10:00:00+07
TV005	DT003	2001221875	ThanhVien	2025-09-20 10:30:00+07
TV006	DT004	2001221876	TruongDoi	2025-09-21 14:00:00+07
TV007	DT004	2001221877	ThanhVien	2025-09-21 14:30:00+07
TV008	DT005	2001221878	TruongDoi	2025-08-20 10:00:00+07
TV0JX2WQ2V	DTUYBZHRE4	2001221872	TruongDoi	2025-12-10 16:51:06+07
TVCTVR12DF	DTUYBZHRE4	2001221873	ThanhVien	2025-12-10 16:51:06+07
\.


--
-- TOC entry 5171 (class 0 OID 19766)
-- Dependencies: 244
-- Data for Name: tintuc; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tintuc (matintuc, tieude, noidung, macuocthi, loaitin, hinhanh, tacgia, luotxem, trangthai, ngaydang, ngaycapnhat) FROM stdin;
TT002	Công bố kết quả cuộc thi COATI	Kết quả cuộc thi đã được công bố, chúc mừng các đội đạt giải	CT001	TinTuc	\N	GV001	200	Published	2024-06-30 17:00:00+07	2025-11-17 02:29:37.895936+07
TT003	Hội thảo Khoa học Dữ liệu 2024	Chương trình hội thảo với các diễn giả hàng đầu trong lĩnh vực Data Science	CT002	SuKien	\N	GV001	180	Published	2024-04-20 10:00:00+07	2025-11-17 02:29:37.895936+07
TT006	Khai mạc Hackathon AI 2025	Cuộc thi Hackathon AI 2025 với chủ đề Smart City chính thức khai mạc	CT005	TinTuc	\N	GV001	180	Published	2025-10-15 08:00:00+07	2025-11-17 02:41:47.933688+07
TT007	Công bố kết quả Web Design Contest	Ban tổ chức công bố kết quả cuộc thi thiết kế website, chúc mừng các đội đạt giải	CT008	TinTuc	\N	GV002	220	Published	2025-09-30 17:00:00+07	2025-11-17 02:41:47.933688+07
TT001	Thông báo khai mạc cuộc thi COATI	Cuộc thi nghiên cứu ứng dụng thuật toán COATI chính thức khai mạc	CT001	ThongBao	\N	GV001	151	Published	2024-03-01 08:00:00+07	2025-11-17 02:29:37.895936+07
TT008	Thông báo tổ chức Olympic Tin học	Olympic Tin học Sinh viên 2025 sẽ được tổ chức vào ngày 20/11/2025	CT006	ThongBao	\N	GV002	167	Published	2025-10-20 10:00:00+07	2025-11-17 02:41:47.933688+07
TT202512099465	testtintuc	hehêh	\N	TinTuc	news/1765307989_69387655b63a8.png	ADMIN	7	Published	2025-12-09 18:50:15+07	2025-12-09 19:19:49+07
TT005	Thông báo Database Design Challenge 2025	Khoa CNTT thông báo tổ chức cuộc thi Database Design Challenge vào ngày 07/12/2025	CT004	ThongBao	news/1765309379_69387bc352e4e.png	GV002	259	Published	2025-11-07 08:00:00+07	2025-12-09 19:42:59+07
TT004	Olympic Tin học Sinh viên - Vòng loại	Thông báo lịch thi vòng loại Olympic Tin học 2024	CT003	ThongBao	\N	GV002	221	Published	2024-03-25 09:00:00+07	2025-11-17 02:29:37.895936+07
\.


--
-- TOC entry 5172 (class 0 OID 19776)
-- Dependencies: 245
-- Data for Name: vongthi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vongthi (mavongthi, tenvongthi, macuocthi, thutu, thoigianbatdau, thoigianketthuc, diadiem, mota, trangthai) FROM stdin;
VT001	Vòng Nộp đề cương	CT001	1	2024-03-01 08:00:00+07	2024-04-30 17:00:00+07	Online	Nộp đề cương nghiên cứu	Completed
VT002	Vòng Báo cáo kết quả	CT001	2	2024-06-15 08:00:00+07	2024-06-20 17:00:00+07	Hội trường A	Trình bày kết quả nghiên cứu	Completed
VT003	Vòng loại Olympic	CT003	1	2024-04-01 08:00:00+07	2024-04-15 17:00:00+07	Phòng máy A	Vòng loại thi lập trình	Completed
VT004	Vòng chung kết Olympic	CT003	2	2024-04-25 08:00:00+07	2024-04-30 17:00:00+07	Phòng máy B	Vòng chung kết thi lập trình	Completed
VT005	Vòng Sơ khảo	CT004	1	2025-12-07 07:45:00+07	2025-12-07 08:45:00+07	Phòng B205, B401, B502	Thi trắc nghiệm lý thuyết	InProgress
VT006	Vòng Chung kết	CT004	2	2025-12-07 13:30:00+07	2025-12-07 14:30:00+07	Phòng A204, A209	Thi thực hành thiết kế CSDL	Pending
\.


--
-- TOC entry 5186 (class 0 OID 0)
-- Dependencies: 246
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 3, true);


--
-- TOC entry 5187 (class 0 OID 0)
-- Dependencies: 250
-- Name: password_reset_otps_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.password_reset_otps_id_seq', 14, true);


--
-- TOC entry 4846 (class 2606 OID 19783)
-- Name: baithi baithi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.baithi
    ADD CONSTRAINT baithi_pkey PRIMARY KEY (mabaithi);


--
-- TOC entry 4848 (class 2606 OID 19785)
-- Name: ban ban_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ban
    ADD CONSTRAINT ban_pkey PRIMARY KEY (maban);


--
-- TOC entry 4850 (class 2606 OID 19787)
-- Name: bomon bomon_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bomon
    ADD CONSTRAINT bomon_pkey PRIMARY KEY (mabomon);


--
-- TOC entry 4852 (class 2606 OID 19789)
-- Name: chiphi chiphi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chiphi
    ADD CONSTRAINT chiphi_pkey PRIMARY KEY (machiphi);


--
-- TOC entry 4926 (class 2606 OID 20185)
-- Name: cocaugiaithuong cocaugiaithuong_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cocaugiaithuong
    ADD CONSTRAINT cocaugiaithuong_pkey PRIMARY KEY (macocau);


--
-- TOC entry 4854 (class 2606 OID 19791)
-- Name: congviec congviec_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.congviec
    ADD CONSTRAINT congviec_pkey PRIMARY KEY (macongviec);


--
-- TOC entry 4856 (class 2606 OID 19793)
-- Name: cuocthi cuocthi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cuocthi
    ADD CONSTRAINT cuocthi_pkey PRIMARY KEY (macuocthi);


--
-- TOC entry 4858 (class 2606 OID 19795)
-- Name: dangkycanhan dangkycanhan_macuocthi_masinhvien_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkycanhan
    ADD CONSTRAINT dangkycanhan_macuocthi_masinhvien_key UNIQUE (macuocthi, masinhvien);


--
-- TOC entry 4860 (class 2606 OID 19797)
-- Name: dangkycanhan dangkycanhan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkycanhan
    ADD CONSTRAINT dangkycanhan_pkey PRIMARY KEY (madangkycanhan);


--
-- TOC entry 4862 (class 2606 OID 19799)
-- Name: dangkydoithi dangkydoithi_macuocthi_madoithi_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkydoithi
    ADD CONSTRAINT dangkydoithi_macuocthi_madoithi_key UNIQUE (macuocthi, madoithi);


--
-- TOC entry 4864 (class 2606 OID 19801)
-- Name: dangkydoithi dangkydoithi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkydoithi
    ADD CONSTRAINT dangkydoithi_pkey PRIMARY KEY (madangkydoi);


--
-- TOC entry 4866 (class 2606 OID 19803)
-- Name: dangkyhoatdong dangkyhoatdong_mahoatdong_masinhvien_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkyhoatdong
    ADD CONSTRAINT dangkyhoatdong_mahoatdong_masinhvien_key UNIQUE (mahoatdong, masinhvien);


--
-- TOC entry 4868 (class 2606 OID 19805)
-- Name: dangkyhoatdong dangkyhoatdong_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkyhoatdong
    ADD CONSTRAINT dangkyhoatdong_pkey PRIMARY KEY (madangkyhoatdong);


--
-- TOC entry 4870 (class 2606 OID 19807)
-- Name: datgiai datgiai_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.datgiai
    ADD CONSTRAINT datgiai_pkey PRIMARY KEY (madatgiai);


--
-- TOC entry 4872 (class 2606 OID 19809)
-- Name: dethi dethi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dethi
    ADD CONSTRAINT dethi_pkey PRIMARY KEY (madethi);


--
-- TOC entry 4874 (class 2606 OID 19811)
-- Name: diemdanhlichhoc diemdanhlichhoc_malichhoc_masinhvien_ngayhoc_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhlichhoc
    ADD CONSTRAINT diemdanhlichhoc_malichhoc_masinhvien_ngayhoc_key UNIQUE (malichhoc, masinhvien, ngayhoc);


--
-- TOC entry 4876 (class 2606 OID 19813)
-- Name: diemdanhlichhoc diemdanhlichhoc_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhlichhoc
    ADD CONSTRAINT diemdanhlichhoc_pkey PRIMARY KEY (madiemdanh);


--
-- TOC entry 4878 (class 2606 OID 19815)
-- Name: diemdanhqr diemdanhqr_maqr_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhqr
    ADD CONSTRAINT diemdanhqr_maqr_key UNIQUE (maqr);


--
-- TOC entry 4880 (class 2606 OID 19817)
-- Name: diemdanhqr diemdanhqr_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhqr
    ADD CONSTRAINT diemdanhqr_pkey PRIMARY KEY (madiemdanh);


--
-- TOC entry 4882 (class 2606 OID 19819)
-- Name: diemrenluyen diemrenluyen_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemrenluyen
    ADD CONSTRAINT diemrenluyen_pkey PRIMARY KEY (madiemrl);


--
-- TOC entry 4884 (class 2606 OID 19821)
-- Name: doithi doithi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doithi
    ADD CONSTRAINT doithi_pkey PRIMARY KEY (madoithi);


--
-- TOC entry 4928 (class 2606 OID 20202)
-- Name: gangiaithuong gangiaithuong_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gangiaithuong
    ADD CONSTRAINT gangiaithuong_pkey PRIMARY KEY (magangiai);


--
-- TOC entry 4886 (class 2606 OID 19823)
-- Name: giangvien giangvien_manguoidung_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.giangvien
    ADD CONSTRAINT giangvien_manguoidung_key UNIQUE (manguoidung);


--
-- TOC entry 4888 (class 2606 OID 19825)
-- Name: giangvien giangvien_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.giangvien
    ADD CONSTRAINT giangvien_pkey PRIMARY KEY (magiangvien);


--
-- TOC entry 4890 (class 2606 OID 19827)
-- Name: hoatdonghotro hoatdonghotro_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hoatdonghotro
    ADD CONSTRAINT hoatdonghotro_pkey PRIMARY KEY (mahoatdong);


--
-- TOC entry 4892 (class 2606 OID 19829)
-- Name: kehoachcuocthi kehoachcuocthi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kehoachcuocthi
    ADD CONSTRAINT kehoachcuocthi_pkey PRIMARY KEY (makehoach);


--
-- TOC entry 4894 (class 2606 OID 19831)
-- Name: ketquathi ketquathi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ketquathi
    ADD CONSTRAINT ketquathi_pkey PRIMARY KEY (maketqua);


--
-- TOC entry 4896 (class 2606 OID 19833)
-- Name: lichhoc lichhoc_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lichhoc
    ADD CONSTRAINT lichhoc_pkey PRIMARY KEY (malichhoc);


--
-- TOC entry 4898 (class 2606 OID 19835)
-- Name: lop lop_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lop
    ADD CONSTRAINT lop_pkey PRIMARY KEY (malop);


--
-- TOC entry 4924 (class 2606 OID 20148)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 4900 (class 2606 OID 19837)
-- Name: monhoc monhoc_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monhoc
    ADD CONSTRAINT monhoc_pkey PRIMARY KEY (mamonhoc);


--
-- TOC entry 4902 (class 2606 OID 19839)
-- Name: nguoidung nguoidung_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nguoidung
    ADD CONSTRAINT nguoidung_email_key UNIQUE (email);


--
-- TOC entry 4904 (class 2606 OID 19841)
-- Name: nguoidung nguoidung_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nguoidung
    ADD CONSTRAINT nguoidung_pkey PRIMARY KEY (manguoidung);


--
-- TOC entry 4906 (class 2606 OID 19843)
-- Name: nguoidung nguoidung_tendangnhap_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nguoidung
    ADD CONSTRAINT nguoidung_tendangnhap_key UNIQUE (tendangnhap);


--
-- TOC entry 4931 (class 2606 OID 20245)
-- Name: password_reset_otps password_reset_otps_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_otps
    ADD CONSTRAINT password_reset_otps_pkey PRIMARY KEY (id);


--
-- TOC entry 4908 (class 2606 OID 19845)
-- Name: phanconggiangvien phanconggiangvien_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.phanconggiangvien
    ADD CONSTRAINT phanconggiangvien_pkey PRIMARY KEY (maphancong);


--
-- TOC entry 4910 (class 2606 OID 19847)
-- Name: quyettoan quyettoan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyettoan
    ADD CONSTRAINT quyettoan_pkey PRIMARY KEY (maquyettoan);


--
-- TOC entry 4912 (class 2606 OID 19849)
-- Name: sinhvien sinhvien_manguoidung_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sinhvien
    ADD CONSTRAINT sinhvien_manguoidung_key UNIQUE (manguoidung);


--
-- TOC entry 4914 (class 2606 OID 19851)
-- Name: sinhvien sinhvien_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sinhvien
    ADD CONSTRAINT sinhvien_pkey PRIMARY KEY (masinhvien);


--
-- TOC entry 4916 (class 2606 OID 19853)
-- Name: thanhviendoithi thanhviendoithi_madoithi_masinhvien_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thanhviendoithi
    ADD CONSTRAINT thanhviendoithi_madoithi_masinhvien_key UNIQUE (madoithi, masinhvien);


--
-- TOC entry 4918 (class 2606 OID 19855)
-- Name: thanhviendoithi thanhviendoithi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thanhviendoithi
    ADD CONSTRAINT thanhviendoithi_pkey PRIMARY KEY (mathanhvien);


--
-- TOC entry 4920 (class 2606 OID 19857)
-- Name: tintuc tintuc_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tintuc
    ADD CONSTRAINT tintuc_pkey PRIMARY KEY (matintuc);


--
-- TOC entry 4922 (class 2606 OID 19859)
-- Name: vongthi vongthi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vongthi
    ADD CONSTRAINT vongthi_pkey PRIMARY KEY (mavongthi);


--
-- TOC entry 4929 (class 1259 OID 20246)
-- Name: password_reset_otps_email_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX password_reset_otps_email_index ON public.password_reset_otps USING btree (email);


--
-- TOC entry 4993 (class 2606 OID 20186)
-- Name: cocaugiaithuong cocaugiaithuong_macuocthi_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cocaugiaithuong
    ADD CONSTRAINT cocaugiaithuong_macuocthi_fkey FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4932 (class 2606 OID 19861)
-- Name: baithi fk_baithi_dangkycanhan; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.baithi
    ADD CONSTRAINT fk_baithi_dangkycanhan FOREIGN KEY (madangkycanhan) REFERENCES public.dangkycanhan(madangkycanhan) ON DELETE CASCADE;


--
-- TOC entry 4933 (class 2606 OID 19866)
-- Name: baithi fk_baithi_dangkydoi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.baithi
    ADD CONSTRAINT fk_baithi_dangkydoi FOREIGN KEY (madangkydoi) REFERENCES public.dangkydoithi(madangkydoi) ON DELETE CASCADE;


--
-- TOC entry 4934 (class 2606 OID 19871)
-- Name: baithi fk_baithi_dethi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.baithi
    ADD CONSTRAINT fk_baithi_dethi FOREIGN KEY (madethi) REFERENCES public.dethi(madethi) ON DELETE CASCADE;


--
-- TOC entry 4935 (class 2606 OID 19876)
-- Name: ban fk_ban_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ban
    ADD CONSTRAINT fk_ban_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4936 (class 2606 OID 19881)
-- Name: bomon fk_bomon_truongbomon; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.bomon
    ADD CONSTRAINT fk_bomon_truongbomon FOREIGN KEY (matruongbomon) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4937 (class 2606 OID 19886)
-- Name: chiphi fk_chiphi_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chiphi
    ADD CONSTRAINT fk_chiphi_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4938 (class 2606 OID 20228)
-- Name: chiphi fk_chiphi_gangiai; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chiphi
    ADD CONSTRAINT fk_chiphi_gangiai FOREIGN KEY (magangiai) REFERENCES public.gangiaithuong(magangiai) ON DELETE SET NULL;


--
-- TOC entry 4939 (class 2606 OID 19891)
-- Name: chiphi fk_chiphi_nguoiduyet; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chiphi
    ADD CONSTRAINT fk_chiphi_nguoiduyet FOREIGN KEY (nguoiduyet) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4940 (class 2606 OID 20150)
-- Name: chiphi fk_chiphi_nguoiyeucau; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.chiphi
    ADD CONSTRAINT fk_chiphi_nguoiyeucau FOREIGN KEY (nguoiyeucau) REFERENCES public.giangvien(magiangvien);


--
-- TOC entry 4941 (class 2606 OID 19896)
-- Name: congviec fk_congviec_ban; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.congviec
    ADD CONSTRAINT fk_congviec_ban FOREIGN KEY (maban) REFERENCES public.ban(maban) ON DELETE SET NULL;


--
-- TOC entry 4942 (class 2606 OID 19901)
-- Name: congviec fk_congviec_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.congviec
    ADD CONSTRAINT fk_congviec_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4943 (class 2606 OID 19906)
-- Name: cuocthi fk_cuocthi_bomon; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cuocthi
    ADD CONSTRAINT fk_cuocthi_bomon FOREIGN KEY (mabomon) REFERENCES public.bomon(mabomon) ON DELETE SET NULL;


--
-- TOC entry 4944 (class 2606 OID 20167)
-- Name: cuocthi fk_cuocthi_kehoach; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cuocthi
    ADD CONSTRAINT fk_cuocthi_kehoach FOREIGN KEY (makehoach) REFERENCES public.kehoachcuocthi(makehoach) ON DELETE SET NULL;


--
-- TOC entry 4945 (class 2606 OID 19911)
-- Name: dangkycanhan fk_dangkycanhan_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkycanhan
    ADD CONSTRAINT fk_dangkycanhan_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4946 (class 2606 OID 19916)
-- Name: dangkycanhan fk_dangkycanhan_sinhvien; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkycanhan
    ADD CONSTRAINT fk_dangkycanhan_sinhvien FOREIGN KEY (masinhvien) REFERENCES public.sinhvien(masinhvien) ON DELETE CASCADE;


--
-- TOC entry 4947 (class 2606 OID 19921)
-- Name: dangkydoithi fk_dangkydoi_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkydoithi
    ADD CONSTRAINT fk_dangkydoi_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4948 (class 2606 OID 19926)
-- Name: dangkydoithi fk_dangkydoi_doithi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkydoithi
    ADD CONSTRAINT fk_dangkydoi_doithi FOREIGN KEY (madoithi) REFERENCES public.doithi(madoithi) ON DELETE CASCADE;


--
-- TOC entry 4949 (class 2606 OID 19931)
-- Name: dangkyhoatdong fk_dangkyhoatdong_hoatdong; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkyhoatdong
    ADD CONSTRAINT fk_dangkyhoatdong_hoatdong FOREIGN KEY (mahoatdong) REFERENCES public.hoatdonghotro(mahoatdong) ON DELETE CASCADE;


--
-- TOC entry 4950 (class 2606 OID 19936)
-- Name: dangkyhoatdong fk_dangkyhoatdong_sinhvien; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dangkyhoatdong
    ADD CONSTRAINT fk_dangkyhoatdong_sinhvien FOREIGN KEY (masinhvien) REFERENCES public.sinhvien(masinhvien) ON DELETE CASCADE;


--
-- TOC entry 4951 (class 2606 OID 19941)
-- Name: datgiai fk_datgiai_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.datgiai
    ADD CONSTRAINT fk_datgiai_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4952 (class 2606 OID 19946)
-- Name: datgiai fk_datgiai_dangkycanhan; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.datgiai
    ADD CONSTRAINT fk_datgiai_dangkycanhan FOREIGN KEY (madangkycanhan) REFERENCES public.dangkycanhan(madangkycanhan) ON DELETE CASCADE;


--
-- TOC entry 4953 (class 2606 OID 19951)
-- Name: datgiai fk_datgiai_dangkydoi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.datgiai
    ADD CONSTRAINT fk_datgiai_dangkydoi FOREIGN KEY (madangkydoi) REFERENCES public.dangkydoithi(madangkydoi) ON DELETE CASCADE;


--
-- TOC entry 4954 (class 2606 OID 19956)
-- Name: dethi fk_dethi_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dethi
    ADD CONSTRAINT fk_dethi_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4955 (class 2606 OID 19961)
-- Name: dethi fk_dethi_nguoitao; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.dethi
    ADD CONSTRAINT fk_dethi_nguoitao FOREIGN KEY (nguoitao) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4958 (class 2606 OID 19966)
-- Name: diemdanhqr fk_diemdanh_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhqr
    ADD CONSTRAINT fk_diemdanh_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4959 (class 2606 OID 19971)
-- Name: diemdanhqr fk_diemdanh_hoatdong; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhqr
    ADD CONSTRAINT fk_diemdanh_hoatdong FOREIGN KEY (mahoatdong) REFERENCES public.hoatdonghotro(mahoatdong) ON DELETE CASCADE;


--
-- TOC entry 4960 (class 2606 OID 19976)
-- Name: diemdanhqr fk_diemdanh_sinhvien; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhqr
    ADD CONSTRAINT fk_diemdanh_sinhvien FOREIGN KEY (masinhvien) REFERENCES public.sinhvien(masinhvien) ON DELETE CASCADE;


--
-- TOC entry 4956 (class 2606 OID 19981)
-- Name: diemdanhlichhoc fk_diemdanhlh_lichhoc; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhlichhoc
    ADD CONSTRAINT fk_diemdanhlh_lichhoc FOREIGN KEY (malichhoc) REFERENCES public.lichhoc(malichhoc) ON DELETE CASCADE;


--
-- TOC entry 4957 (class 2606 OID 19986)
-- Name: diemdanhlichhoc fk_diemdanhlh_sinhvien; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemdanhlichhoc
    ADD CONSTRAINT fk_diemdanhlh_sinhvien FOREIGN KEY (masinhvien) REFERENCES public.sinhvien(masinhvien) ON DELETE CASCADE;


--
-- TOC entry 4961 (class 2606 OID 19991)
-- Name: diemrenluyen fk_diemrl_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemrenluyen
    ADD CONSTRAINT fk_diemrl_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE SET NULL;


--
-- TOC entry 4962 (class 2606 OID 20223)
-- Name: diemrenluyen fk_diemrl_gangiai; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemrenluyen
    ADD CONSTRAINT fk_diemrl_gangiai FOREIGN KEY (magangiai) REFERENCES public.gangiaithuong(magangiai) ON DELETE SET NULL;


--
-- TOC entry 4963 (class 2606 OID 19996)
-- Name: diemrenluyen fk_diemrl_hoatdong; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemrenluyen
    ADD CONSTRAINT fk_diemrl_hoatdong FOREIGN KEY (mahoatdong) REFERENCES public.hoatdonghotro(mahoatdong) ON DELETE SET NULL;


--
-- TOC entry 4964 (class 2606 OID 20001)
-- Name: diemrenluyen fk_diemrl_sinhvien; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.diemrenluyen
    ADD CONSTRAINT fk_diemrl_sinhvien FOREIGN KEY (masinhvien) REFERENCES public.sinhvien(masinhvien) ON DELETE CASCADE;


--
-- TOC entry 4965 (class 2606 OID 20006)
-- Name: doithi fk_doithi_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doithi
    ADD CONSTRAINT fk_doithi_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4966 (class 2606 OID 20011)
-- Name: doithi fk_doithi_truongdoi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doithi
    ADD CONSTRAINT fk_doithi_truongdoi FOREIGN KEY (matruongdoi) REFERENCES public.sinhvien(masinhvien) ON DELETE SET NULL;


--
-- TOC entry 4994 (class 2606 OID 20233)
-- Name: gangiaithuong fk_gangiai_nguoigan; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gangiaithuong
    ADD CONSTRAINT fk_gangiai_nguoigan FOREIGN KEY (nguoigan) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4967 (class 2606 OID 20016)
-- Name: giangvien fk_giangvien_bomon; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.giangvien
    ADD CONSTRAINT fk_giangvien_bomon FOREIGN KEY (mabomon) REFERENCES public.bomon(mabomon) ON DELETE SET NULL;


--
-- TOC entry 4968 (class 2606 OID 20021)
-- Name: giangvien fk_giangvien_nguoidung; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.giangvien
    ADD CONSTRAINT fk_giangvien_nguoidung FOREIGN KEY (manguoidung) REFERENCES public.nguoidung(manguoidung) ON DELETE CASCADE;


--
-- TOC entry 4969 (class 2606 OID 20026)
-- Name: hoatdonghotro fk_hoatdong_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.hoatdonghotro
    ADD CONSTRAINT fk_hoatdong_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4970 (class 2606 OID 20157)
-- Name: kehoachcuocthi fk_kehoach_bomon; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kehoachcuocthi
    ADD CONSTRAINT fk_kehoach_bomon FOREIGN KEY (mabomon) REFERENCES public.bomon(mabomon) ON DELETE CASCADE;


--
-- TOC entry 4971 (class 2606 OID 20036)
-- Name: kehoachcuocthi fk_kehoach_nguoiduyet; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kehoachcuocthi
    ADD CONSTRAINT fk_kehoach_nguoiduyet FOREIGN KEY (nguoiduyet) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4972 (class 2606 OID 20162)
-- Name: kehoachcuocthi fk_kehoach_nguoinop; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kehoachcuocthi
    ADD CONSTRAINT fk_kehoach_nguoinop FOREIGN KEY (nguoinop) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4973 (class 2606 OID 20041)
-- Name: ketquathi fk_ketqua_baithi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ketquathi
    ADD CONSTRAINT fk_ketqua_baithi FOREIGN KEY (mabaithi) REFERENCES public.baithi(mabaithi) ON DELETE CASCADE;


--
-- TOC entry 4974 (class 2606 OID 20046)
-- Name: ketquathi fk_ketqua_nguoichamdiem; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.ketquathi
    ADD CONSTRAINT fk_ketqua_nguoichamdiem FOREIGN KEY (nguoichamdiem) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4975 (class 2606 OID 20051)
-- Name: lichhoc fk_lichhoc_giangvien; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lichhoc
    ADD CONSTRAINT fk_lichhoc_giangvien FOREIGN KEY (magiangvien) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4976 (class 2606 OID 20056)
-- Name: lichhoc fk_lichhoc_lop; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lichhoc
    ADD CONSTRAINT fk_lichhoc_lop FOREIGN KEY (malop) REFERENCES public.lop(malop) ON DELETE SET NULL;


--
-- TOC entry 4977 (class 2606 OID 20061)
-- Name: lichhoc fk_lichhoc_monhoc; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lichhoc
    ADD CONSTRAINT fk_lichhoc_monhoc FOREIGN KEY (mamonhoc) REFERENCES public.monhoc(mamonhoc) ON DELETE CASCADE;


--
-- TOC entry 4978 (class 2606 OID 20066)
-- Name: lop fk_lop_giangvienchunhiem; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.lop
    ADD CONSTRAINT fk_lop_giangvienchunhiem FOREIGN KEY (magiangvienchunhiem) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4979 (class 2606 OID 20071)
-- Name: monhoc fk_monhoc_bomon; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.monhoc
    ADD CONSTRAINT fk_monhoc_bomon FOREIGN KEY (mabomon) REFERENCES public.bomon(mabomon) ON DELETE SET NULL;


--
-- TOC entry 4980 (class 2606 OID 20076)
-- Name: phanconggiangvien fk_phancong_ban; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.phanconggiangvien
    ADD CONSTRAINT fk_phancong_ban FOREIGN KEY (maban) REFERENCES public.ban(maban) ON DELETE SET NULL;


--
-- TOC entry 4981 (class 2606 OID 20081)
-- Name: phanconggiangvien fk_phancong_congviec; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.phanconggiangvien
    ADD CONSTRAINT fk_phancong_congviec FOREIGN KEY (macongviec) REFERENCES public.congviec(macongviec) ON DELETE CASCADE;


--
-- TOC entry 4982 (class 2606 OID 20086)
-- Name: phanconggiangvien fk_phancong_giangvien; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.phanconggiangvien
    ADD CONSTRAINT fk_phancong_giangvien FOREIGN KEY (magiangvien) REFERENCES public.giangvien(magiangvien) ON DELETE CASCADE;


--
-- TOC entry 4983 (class 2606 OID 20091)
-- Name: quyettoan fk_quyettoan_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyettoan
    ADD CONSTRAINT fk_quyettoan_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4984 (class 2606 OID 20096)
-- Name: quyettoan fk_quyettoan_nguoiduyet; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyettoan
    ADD CONSTRAINT fk_quyettoan_nguoiduyet FOREIGN KEY (nguoiduyet) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4985 (class 2606 OID 20101)
-- Name: quyettoan fk_quyettoan_nguoilap; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyettoan
    ADD CONSTRAINT fk_quyettoan_nguoilap FOREIGN KEY (nguoilap) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4986 (class 2606 OID 20106)
-- Name: sinhvien fk_sinhvien_lop; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sinhvien
    ADD CONSTRAINT fk_sinhvien_lop FOREIGN KEY (malop) REFERENCES public.lop(malop) ON DELETE SET NULL;


--
-- TOC entry 4987 (class 2606 OID 20111)
-- Name: sinhvien fk_sinhvien_nguoidung; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sinhvien
    ADD CONSTRAINT fk_sinhvien_nguoidung FOREIGN KEY (manguoidung) REFERENCES public.nguoidung(manguoidung) ON DELETE CASCADE;


--
-- TOC entry 4988 (class 2606 OID 20116)
-- Name: thanhviendoithi fk_thanhvien_doithi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thanhviendoithi
    ADD CONSTRAINT fk_thanhvien_doithi FOREIGN KEY (madoithi) REFERENCES public.doithi(madoithi) ON DELETE CASCADE;


--
-- TOC entry 4989 (class 2606 OID 20121)
-- Name: thanhviendoithi fk_thanhvien_sinhvien; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thanhviendoithi
    ADD CONSTRAINT fk_thanhvien_sinhvien FOREIGN KEY (masinhvien) REFERENCES public.sinhvien(masinhvien) ON DELETE CASCADE;


--
-- TOC entry 4990 (class 2606 OID 20126)
-- Name: tintuc fk_tintuc_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tintuc
    ADD CONSTRAINT fk_tintuc_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE SET NULL;


--
-- TOC entry 4991 (class 2606 OID 20131)
-- Name: tintuc fk_tintuc_tacgia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tintuc
    ADD CONSTRAINT fk_tintuc_tacgia FOREIGN KEY (tacgia) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


--
-- TOC entry 4992 (class 2606 OID 20136)
-- Name: vongthi fk_vongthi_cuocthi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vongthi
    ADD CONSTRAINT fk_vongthi_cuocthi FOREIGN KEY (macuocthi) REFERENCES public.cuocthi(macuocthi) ON DELETE CASCADE;


--
-- TOC entry 4995 (class 2606 OID 20203)
-- Name: gangiaithuong gangiaithuong_macocau_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gangiaithuong
    ADD CONSTRAINT gangiaithuong_macocau_fkey FOREIGN KEY (macocau) REFERENCES public.cocaugiaithuong(macocau) ON DELETE CASCADE;


--
-- TOC entry 4996 (class 2606 OID 20208)
-- Name: gangiaithuong gangiaithuong_madangkycanhan_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gangiaithuong
    ADD CONSTRAINT gangiaithuong_madangkycanhan_fkey FOREIGN KEY (madangkycanhan) REFERENCES public.dangkycanhan(madangkycanhan) ON DELETE CASCADE;


--
-- TOC entry 4997 (class 2606 OID 20213)
-- Name: gangiaithuong gangiaithuong_madangkydoi_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gangiaithuong
    ADD CONSTRAINT gangiaithuong_madangkydoi_fkey FOREIGN KEY (madangkydoi) REFERENCES public.dangkydoithi(madangkydoi) ON DELETE CASCADE;


--
-- TOC entry 4998 (class 2606 OID 20218)
-- Name: gangiaithuong gangiaithuong_nguoiduyet_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gangiaithuong
    ADD CONSTRAINT gangiaithuong_nguoiduyet_fkey FOREIGN KEY (nguoiduyet) REFERENCES public.giangvien(magiangvien) ON DELETE SET NULL;


-- Completed on 2025-12-11 01:40:07

--
-- PostgreSQL database dump complete
--

