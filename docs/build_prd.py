#!/usr/bin/env python3
"""Generator PRD PDF — PerpusKita."""
import re
from fpdf import FPDF
from fpdf.fonts import FontFace
from fpdf.enums import TableBordersLayout, TableBordersLayout as TBL, Align, VAlign

FONT_DIR = "/data/data/com.termux/files/usr/share/fonts/TTF"

PRIMARY = (30, 58, 95)
ACCENT = (0, 120, 160)
LIGHT = (235, 241, 247)
LIGHTER = (246, 248, 251)
GRAY = (110, 110, 110)
DARK = (25, 25, 25)

USABLE = 174  # mm


class PRD(FPDF):
    def header(self):
        if self.page_no() == 1:
            return
        self.set_font("DejaVu", "", 8)
        self.set_text_color(*GRAY)
        self.set_y(8)
        self.cell(USABLE / 2, 5, "PRD — PerpusKita v1.0", align="L")
        self.cell(USABLE / 2, 5, "Platform Digital Buku Multi-Role", align="R")
        self.set_draw_color(210, 214, 220)
        y = self.get_y() + 1.5
        self.line(self.l_margin, y, self.l_margin + USABLE, y)
        self.set_y(y + 4)

    def footer(self):
        self.set_y(-14)
        self.set_draw_color(210, 214, 220)
        self.line(self.l_margin, self.get_y(), self.l_margin + USABLE, self.get_y())
        self.ln(1.5)
        self.set_font("DejaVu", "", 8)
        self.set_text_color(*GRAY)
        self.cell(USABLE / 2, 5, "13 Oktober 2026", align="L")
        self.cell(USABLE / 2, 5, f"Halaman {self.page_no()} / {{nb}}", align="R")

    # ---------- rich text ----------
    @staticmethod
    def _md_to_html(text):
        esc = text.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
        esc = re.sub(r"\*\*(.+?)\*\*", r"<b>\1</b>", esc)
        esc = re.sub(r"`(.+?)`", r"<code>\1</code>", esc)
        return esc

    def rich(self, text, size=10, h=5.0):
        """Tulis paragraf dengan **bold** dan `code` inline (wrapping benar via write_html)."""
        self.set_font("DejaVu", "", size)
        self.set_text_color(*DARK)
        self.write_html(self._md_to_html(text))
        self.ln(h)

    # ---------- headings ----------
    def h1(self, num, title):
        self.add_page()
        self.ln(2)
        self.set_fill_color(*PRIMARY)
        self.rect(self.l_margin, self.get_y(), USABLE, 11, "F")
        self.set_text_color(255, 255, 255)
        self.set_font("DejaVu", "B", 14)
        self.set_xy(self.l_margin + 4, self.get_y() + 2.4)
        self.cell(USABLE - 8, 6, f"{num}. {title}")
        self.ln(14)
        self.set_text_color(*DARK)

    def h2(self, num, title):
        self.ln(2)
        self.set_font("DejaVu", "B", 11.5)
        self.set_text_color(*PRIMARY)
        self.cell(0, 7, f"{num} {title}", new_x="LMARGIN", new_y="NEXT")
        self.set_draw_color(*ACCENT)
        self.set_line_width(0.6)
        y = self.get_y()
        self.line(self.l_margin, y, self.l_margin + 40, y)
        self.set_line_width(0.2)
        self.ln(3)
        self.set_text_color(*DARK)

    def h3(self, title):
        self.ln(1.5)
        self.set_font("DejaVu", "B", 10.5)
        self.set_text_color(*ACCENT)
        self.cell(0, 6, title, new_x="LMARGIN", new_y="NEXT")
        self.ln(1)
        self.set_text_color(*DARK)

    # ---------- blocks ----------
    def para(self, text, size=10):
        self.rich(text, size=size)
        self.ln(1)

    def bullets(self, items, size=10):
        self.set_text_color(*DARK)
        for it in items:
            self.set_x(self.l_margin + 2)
            self.set_font("DejaVu", "", size)
            self.set_text_color(*DARK)
            self.write_html(
                "<font color='#0078A0'><b>" + chr(8226) + "</b></font> " + self._md_to_html(it)
            )
            self.ln(5.6)
        self.ln(1.5)

    def numbered(self, items, size=10):
        for i, it in enumerate(items, 1):
            self.set_x(self.l_margin + 2)
            self.set_font("DejaVu", "", size)
            self.set_text_color(*DARK)
            self.write_html(
                "<font color='#0078A0'><b>" + f"{i}." + "</b></font> " + self._md_to_html(it)
            )
            self.ln(5.6)
        self.ln(1)

    def code(self, text):
        self.ln(1)
        self.set_font("DejaVuMono", "", 7.8)
        self.set_text_color(40, 44, 52)
        self.set_fill_color(243, 245, 248)
        self.multi_cell(USABLE, 4.3, text, fill=True)
        self.ln(2)

    def note(self, text):
        self.ln(1)
        self.set_font("DejaVu", "", 9)
        self.set_text_color(20, 60, 90)
        self.set_fill_color(232, 242, 248)
        self.set_draw_color(*ACCENT)
        self.multi_cell(USABLE, 5, f"  CATATAN: {text}", fill=True, border="L")
        self.ln(2)
        self.set_text_color(*DARK)

    def table(self, headers, rows, widths, aligns=None, size=8.5):
        self.ln(1)
        self.set_font("DejaVu", "", size)
        self.set_text_color(*DARK)
        aligns = aligns or ["LEFT"] * len(headers)
        amap = {"LEFT": Align.L, "CENTER": Align.C, "RIGHT": Align.R, "JUSTIFY": Align.J}
        with super().table(
            col_widths=widths,
            text_align=Align.J,
            padding=(1.4, 1.6, 1.4, 1.6),
            line_height=4.4,
            borders_layout=TableBordersLayout.ALL,
            headings_style=FontFace(emphasis="BOLD", color=(255, 255, 255), fill_color=PRIMARY),
            first_row_as_headings=True,
            width=USABLE,
            markdown=True,
            gutter_height=0,
        ) as t:
            hr = t.row(style=FontFace(emphasis="BOLD", color=(255, 255, 255), fill_color=PRIMARY))
            for h, a in zip(headers, aligns):
                hr.cell(h, align=amap[a])
            for i, r in enumerate(rows):
                row = t.row(style=FontFace(fill_color=LIGHTER if i % 2 else (255, 255, 255)))
                for c, a in zip(r, aligns):
                    row.cell(str(c), align=amap[a])
        self.ln(2)

    def pagebreak(self):
        self.add_page()


def build():
    pdf = PRD(orientation="P", unit="mm", format="A4")
    pdf.add_font("DejaVu", "", f"{FONT_DIR}/DejaVuSans.ttf")
    pdf.add_font("DejaVu", "B", f"{FONT_DIR}/DejaVuSans-Bold.ttf")
    pdf.add_font("DejaVu", "I", f"{FONT_DIR}/DejaVuSans-Oblique.ttf")
    pdf.add_font("DejaVu", "BI", f"{FONT_DIR}/DejaVuSans-BoldOblique.ttf")
    pdf.add_font("DejaVuMono", "", f"{FONT_DIR}/DejaVuSansMono.ttf")
    pdf.add_font("DejaVuMono", "B", f"{FONT_DIR}/DejaVuSansMono-Bold.ttf")
    pdf.set_margins(18, 16, 18)
    pdf.set_auto_page_break(True, margin=18)
    pdf.alias_nb_pages()

    # ================= COVER =================
    pdf.add_page()
    pdf.set_fill_color(*PRIMARY)
    pdf.rect(0, 0, 210, 78, "F")
    pdf.set_fill_color(*ACCENT)
    pdf.rect(0, 78, 210, 3, "F")
    pdf.set_text_color(255, 255, 255)
    pdf.set_font("DejaVu", "B", 11)
    pdf.set_xy(18, 20)
    pdf.cell(174, 7, "PRODUCT REQUIREMENTS DOCUMENT (PRD)")
    pdf.set_font("DejaVu", "B", 26)
    pdf.set_xy(18, 32)
    pdf.cell(174, 13, "PerpusKita")
    pdf.set_font("DejaVu", "", 14)
    pdf.set_xy(18, 48)
    pdf.cell(174, 8, "Platform Digital Buku Multi-Role")
    pdf.set_font("DejaVu", "", 10.5)
    pdf.set_xy(18, 58)
    pdf.cell(174, 6, "Pembaca  |  Penulis  |  Penerbit  |  Super Admin")

    pdf.set_text_color(*DARK)
    pdf.set_font("DejaVu", "B", 10)
    pdf.set_xy(18, 95)
    pdf.cell(174, 7, "Informasi Dokumen")

    meta = [
        ["Judul", "PRD — PerpusKita: Platform Digital Buku Multi-Role"],
        ["Versi", "1.0 (Final)"],
        ["Tanggal", "13 Oktober 2026"],
        ["Status", "Siap Implementasi"],
        ["Cakupan", "Full-Featured (10 fase pengembangan)"],
        ["Stack", "Laravel 13 · Vue 3 · Inertia · Tailwind CSS · MySQL 8 · Redis · Cloudflare R2 · Midtrans"],
        ["Storage", "Cloudflare R2 (S3-compatible), seluruh akses server-side"],
        ["Pembacaan", "Direkomendasikan untuk tim produk, engineering, dan stakeholder"],
    ]
    pdf.set_y(104)
    pdf.table(
        ["Label", "Keterangan"],
        meta,
        widths=(42, 132),
        aligns=("LEFT", "LEFT"),
        size=9,
    )

    pdf.set_y(175)
    pdf.set_font("DejaVu", "", 9)
    pdf.set_text_color(*GRAY)
    pdf.multi_cell(
        174,
        5,
        "Dokumen ini memuat seluruh rencana produk: role & hak akses, spesifikasi fitur per modul, "
        "arsitektur teknis, skema data, alur kritis transaksi dan royalti, peta halaman, persyaratan "
        "non-fungional, peta jalan 10 fase, asumsi, serta risiko. Semua keputusan desain di dalamnya "
        "merupakan hasil kesepakatan sesi perencanaan.",
        align="J",
    )

    # ================= DAFTAR ISI =================
    pdf.add_page()
    pdf.ln(2)
    pdf.set_font("DejaVu", "B", 16)
    pdf.set_text_color(*PRIMARY)
    pdf.cell(0, 10, "Daftar Isi", new_x="LMARGIN", new_y="NEXT")
    pdf.ln(3)
    toc = [
        ("1", "Ringkasan Produk"),
        ("2", "Tujuan & Ukuran Keberhasilan"),
        ("3", "Role, Hak Akses & Registrasi"),
        ("4", "Spesifikasi Fitur"),
        ("4.1", "Landing Page (Halaman Awal)"),
        ("4.2", "Autentikasi & Profil"),
        ("4.3", "Katalog & Pencarian"),
        ("4.4", "Manajemen Buku (Penulis & Penerbit)"),
        ("4.5", "Reader EPUB & PDF"),
        ("4.6", "Keranjang, Checkout & Pembayaran"),
        ("4.7", "Sistem Promo: Diskon, Voucher, Flash Sale, Poin"),
        ("4.8", "Wishlist & Notifikasi"),
        ("4.9", "Rating & Komentar"),
        ("4.10", "Gamifikasi & Engagement"),
        ("4.11", "Royalti & Kemitraan Penerbit"),
        ("4.12", "Penarikan Dana"),
        ("4.13", "Dashboard Super Admin"),
        ("5", "Arsitektur & Teknologi"),
        ("6", "Model Data"),
        ("7", "Alur Kritis"),
        ("8", "Peta Halaman & Rute"),
        ("9", "Persyaratan Non-Fungional"),
        ("10", "Peta Jalan Implementasi (10 Fase)"),
        ("11", "Asumsi, Batasan & Out of Scope"),
        ("12", "Risiko & Mitigasi"),
    ]
    for num, title in toc:
        indent = 6 if "." in num else 0
        pdf.set_x(18 + indent)
        pdf.set_font("DejaVu", "B" if indent == 0 else "", 10.5)
        pdf.set_text_color(*(PRIMARY if indent == 0 else DARK))
        pdf.cell(14, 7, num)
        pdf.set_font("DejaVu", "B" if indent == 0 else "", 10.5)
        pdf.cell(0, 7, title, new_x="LMARGIN", new_y="NEXT")

    # ================= 1. RINGKASAN =================
    pdf.h1(1, "Ringkasan Produk")
    pdf.h2("1.1", "Latar Belakang")
    pdf.para(
        "Konsumsi buku digital di Indonesia terus tumbuh, namun mayoritas platform masih terbagi antara "
        "tempat menjual buku, tempat membaca, dan kanal penerbit yang tidak saling terhubung. **PerpusKita** "
        "hadir sebagai satu platform terpadu yang menggabungkan **toko buku digital**, **reader nyaman** "
        "(EPUB & PDF), **sistem kemitraan penerbit dengan bagi hasil (royalti)**, serta **mesin promosi** "
        "(diskon, voucher, flash sale, poin) agar pembaca betah berlama-lama dan penulis/penerbit memperoleh "
        "penghasilan yang transparan."
    )
    pdf.h2("1.2", "Tujuan Produk")
    pdf.bullets([
        "Menyediakan pengalaman membaca buku digital yang nyaman langsung di browser (tanpa aplikasi).",
        "Memberi penulis dan penerbit kanal publikasi & penjualan mandiri dengan onboarding **instan tanpa verifikasi admin**.",
        "Membangun sistem royalti yang adil dan auditabel antara penulis dan penerbit melalui kemitraan.",
        "Mendorong keterlibatan (engagement) pembaca melalui promo, gamifikasi, dan rekomendasi.",
        "Menyediakan super admin dengan kontrol penuh atas konten, transaksi, pengguna, dan konfigurasi platform.",
    ])
    pdf.h2("1.3", "Target Pengguna")
    pdf.table(
        ["Persona", "Kebutuhan Utama"],
        [
            ["Pembaca (User)", "Menjelajah, membeli, membaca, berkomentar, memanfaatkan promo & poin, menyimpan wishlist."],
            ["Penulis", "Mengunggah & menjual buku, mengatur diskon, memantau penjualan dan royalti, menarik dana."],
            ["Penerbit", "Semua fitur penulis + mengelola imprint, kemitraan penulis, bagi hasil, dan pemasaran buku mitra."],
            ["Super Admin", "Mengatur seluruh platform: konten, pengguna, promo, transaksi, kemitraan, dan pengaturan situs."],
        ],
        widths=(40, 134),
    )
    pdf.h2("1.4", "Nilai Jual Utama")
    pdf.bullets([
        "**Reader bawaan** — EPUB & PDF dibaca langsung di web dengan progres, bookmark, highlight, dan tema baca.",
        "**Onboarding kilat** — penulis & penerbit langsung aktif saat mendaftar; penerbit memiliki halaman pendaftaran khusus.",
        "**Mesin promo lengkap** — diskon terjadwal, voucher, flash sale berhitung mundur, poin reward, buku gratis bulanan.",
        "**Bagi hasil transparan** — ledger royalti per transaksi untuk penulis dan penerbit, plus penarikan dana.",
        "**Aman** — seluruh file disimpan di Cloudflare R2 dan diakses sepenuhnya **server-side** lewat URL sementara.",
    ])

    # ================= 2. TUJUAN =================
    pdf.h1(2, "Tujuan & Ukuran Keberhasilan")
    pdf.para("Target kuantitatif bersifat indikatif dan akan disesuaikan setelah peluncuran (baseline 3 bulan pertama):")
    pdf.table(
        ["Area", "Indikator", "Target Awal"],
        [
            ["Akuisisi", "Registrasi baru / bulan", "1.000+"],
            ["Aktivasi", "Pengguna yang membaca minimal 1 buku", "40% dari registrasi"],
            ["Retensi", "Reading streak >= 7 hari", "15% pembaca aktif"],
            ["Transaksi", "Konversi keranjang -> pesanan dibayar", ">= 25%"],
            ["Konten", "Buku terbit / bulan", "200+"],
            ["Kemitraan", "Penerbit aktif dengan minimal 1 kemitraan", "20 penerbit"],
            ["Kualitas", "Rating rata-rata buku", ">= 4,0 dari 5"],
        ],
        widths=(34, 96, 44),
        aligns=("LEFT", "LEFT", "CENTER"),
    )

    # ================= 3. ROLE =================
    pdf.h1(3, "Role, Hak Akses & Registrasi")
    pdf.h2("3.1", "Matriks Hak Akses")
    pdf.table(
        ["Fitur", "Pembaca", "Penulis", "Penerbit", "Super Admin"],
        [
            ["Jelajahi katalog & cari buku", "Ya", "Ya", "Ya", "Ya"],
            ["Membeli & membaca buku", "Ya", "Ya", "Ya", "Ya"],
            ["Rating, komentar, wishlist", "Ya", "Ya", "Ya", "Ya"],
            ["Upload & menjual buku", "Tidak", "Ya", "Ya", "Ya"],
            ["Diskon per buku", "Tidak", "Ya", "Ya", "Ya"],
            ["Membuat voucher", "Tidak", "Tidak", "Ya", "Ya"],
            ["Melihat laporan penjualan & royalti", "Tidak", "Ya", "Ya", "Ya"],
            ["Ajukan kemitraan (kerja sama)", "Tidak", "Ya", "Ya", "-"],
            ["Kelola mitra & bagi hasil", "Tidak", "Tidak", "Ya", "Ya"],
            ["Ajukan penarikan dana", "Tidak", "Ya", "Ya", "Ya"],
            ["Moderasi buku & komentar", "Tidak", "Tidak", "Tidak", "Ya"],
            ["Kelola voucher global & flash sale", "Tidak", "Tidak", "Tidak", "Ya"],
            ["Verifikasi penarikan & kemitraan", "Tidak", "Tidak", "Tidak", "Ya"],
            ["Manajemen user (ban/role) & kategori", "Tidak", "Tidak", "Tidak", "Ya"],
            ["Dashboard statistik global & site settings", "Tidak", "Tidak", "Tidak", "Ya"],
        ],
        widths=(74, 25, 25, 25, 25),
        aligns=("LEFT", "CENTER", "CENTER", "CENTER", "CENTER"),
    )
    pdf.h2("3.2", "Registrasi & Alur Role")
    pdf.numbered([
        "**Registrasi umum (`/register`)** — untuk **Pembaca** dan **Penulis**. Pengguna memilih role saat mendaftar "
        "(default: Pembaca). Role **langsung aktif**, tanpa verifikasi admin. FR-3.1",
        "**Pendaftaran penerbit (`/daftar-penerbit`)** — halaman terpisah khusus penerbit, berisi pengantar keuntungan "
        "menjadi penerbit serta form khusus: email, kata sandi, nama imprint/penerbit, nama badan usaha (opsional), alamat, "
        "telepon, dan info rekening bank untuk penarikan. Akun `role=publisher` **langsung aktif** tanpa verifikasi. FR-3.2",
        "**Upgrade role dari profil** — Pembaca dapat naik menjadi Penulis kapan saja dari halaman profil; konversi ke "
        "Penerbit mengarahkan ke `/daftar-penerbit` atau form upgrade setara. FR-3.3",
        "**Persetujuan admin hanya untuk urusan uang & kerja sama** — approve penarikan dana dan approve kemitraan "
        "penulis-penerbit (bagi hasil). Selain itu semua proses bersifat otonom. FR-3.4",
        "**Keamanan akun** — login, reset kata sandi via email, pemblokiran (ban) oleh super admin, dan proteksi "
        "rate-limit pada endpoint autentikasi. FR-3.5",
    ])
    pdf.note(
        "Persetujuan kemitraan dan penarikan dana tetap memerlukan super admin agar pembagian royalti dan aliran dana "
        "selalu terkontrol. Registrasi role itu sendiri tidak menunggu admin."
    )

    # ================= 4. FITUR =================
    pdf.h1(4, "Spesifikasi Fitur")

    pdf.h2("4.1", "Landing Page (Halaman Awal)")
    pdf.para(
        "Halaman `/` adalah halaman pertama yang diakses pengguna — baik tamu maupun pengguna login. "
        "Bersifat marketing sekaligus etalase katalog."
    )
    pdf.bullets([
        "FR-4.1.1 Hero dengan headline, subjudul, dan CTA **'Mulai Membaca'** serta **'Daftar Penulis'** / **'Jadi Penerbit'**.",
        "FR-4.1.2 Section **Flash Sale dengan hitung mundur (countdown) live** dan kartu buku berharga promo.",
        "FR-4.1.3 Koleksi unggulan, kategori populer, buku terlaris, dan buku terbaru.",
        "FR-4.1.4 Section keunggulan platform (reader nyaman, voucher & poin, royalti transparan) + statistik platform "
        "(jumlah buku, penulis, pembaca).",
        "FR-4.1.5 Testimoni dan CTA pendaftaran berulang di akhir halaman.",
        "FR-4.1.6 Form newsletter (subscribe email) di footer area.",
        "FR-4.1.7 Bila sudah login: kartu **'Lanjutkan Membaca'** (progress terakhir) menggantikan CTA tamu.",
        "FR-4.1.8 Footer: tentang, bantuan, syarat & ketentuan, kebijakan privasi, tautan sosial media.",
        "FR-4.1.9 Konten dapat diakses tanpa login; SEO-friendly (meta title/description, Open Graph).",
    ])

    pdf.h2("4.2", "Autentikasi & Profil")
    pdf.bullets([
        "FR-4.2.1 Login, registrasi umum, lupa/reset kata sandi via email.",
        "FR-4.2.2 Halaman profil: nama, avatar, bio singkat, informasi bank (penulis/penerbit), ganti kata sandi.",
        "FR-4.2.3 Riwayat aktivitas: pesanan, penukaran poin, badge, streak membaca.",
        "FR-4.2.4 Preferensi baca tersimpan per pengguna (tema, ukuran font, line-height, lebar halaman).",
        "FR-4.2.5 Pengaturan notifikasi (email & in-app) per jenis notifikasi.",
        "FR-4.2.6 Pilihan role saat registrasi umum (Pembaca/Penulis); tautan ke halaman khusus penerbit.",
    ])

    pdf.h2("4.3", "Katalog & Pencarian")
    pdf.bullets([
        "FR-4.3.1 Halaman `/buku`: grid buku dengan filter kategori, rentang harga, rating, sedang promo; "
        "urutan (terlaris, terbaru, termurah, rating tertinggi); pencarian teks penuh (MySQL FULLTEXT); paginasi.",
        "FR-4.3.2 Halaman detail `/buku/{slug}`: sinopsis, penulis/penerbit, harga & harga coret, sampel gratis, "
        "rating rata-rata, jumlah komentar, buku terkait, tombol beli/keranjang & wishlist.",
        "FR-4.3.3 Penanda 'Diskon', 'Flash Sale', 'Gratis', dan 'Buku Bulan Ini' pada kartu buku.",
        "FR-4.3.4 Halaman kategori (`/kategori/{slug}`) dan halaman penulis/penerbit publik.",
        "FR-4.3.5 Metadata SEO per halaman (title, description, OG image = cover buku).",
    ])

    pdf.h2("4.4", "Manajemen Buku (Penulis & Penerbit)")
    pdf.bullets([
        "FR-4.4.1 CRUD buku: judul, slug, sinopsis, ISBN (opsional), bahasa, kategori, tag, harga, file EPUB/PDF, cover, "
        "sampel (bab/awal halaman).",
        "FR-4.4.2 **Upload server-side**: file dikirim ke controller Laravel, divalidasi (ekstensi, magic bytes, ukuran), "
        "lalu disimpan ke Cloudflare R2 melalui `Storage` facade — tanpa kredensial di browser.",
        "FR-4.4.3 Status buku: `draft -> pending -> published / rejected`. Buku baru langsung bisa diajukan; moderasi admin "
        "adalah kontrol mutu konten setelah pengajuan (bukan syarat untuk mendaftar/mengunggah).",
        "FR-4.4.4 Penulis/penerbit dapat mengatur **diskon per buku** (harga coret + jadwal mulai/berakhir).",
        "FR-4.4.5 Publisher dapat menandai buku imprint/mitra sebagai unggulan & mengatur komisi pemasaran.",
        "FR-4.4.6 Laporan penjualan per buku (periode, jumlah terjual, royalti) dan ekspor CSV.",
        "FR-4.4.7 Log komentar masuk pada buku miliknya.",
    ])

    pdf.h2("4.5", "Reader EPUB & PDF")
    pdf.bullets([
        "FR-4.5.1 Halaman `/baca/{book}`: reader fullscreen — EPUB via **epub.js**, PDF via **pdf.js**.",
        "FR-4.5.2 File diambil dengan **temporary URL** yang dibuat server-side (berlaku singkat, tidak dapat dibagikan permanen).",
        "FR-4.5.3 Progres baca otomatis tersimpan (posisi CFI untuk EPUB, nomor halaman untuk PDF) dan dapat dilanjutkan "
        "dari perangkat lain.",
        "FR-4.5.4 Bookmark dan highlight (cuplikan teks + posisi).",
        "FR-4.5.5 Preferensi tampilan: tema terang/sepia/gelap, ukuran font, line-height, lebar halaman.",
        "FR-4.5.6 Navigasi: daftar isi, geser halaman/bab, bilah progres, pencarian teks dalam buku (EPUB).",
        "FR-4.5.7 Akses hanya untuk pembeli (atau buku gratis/sampel); penulis-penerbit memilikinya sendiri; admin berwenang.",
    ])

    pdf.h2("4.6", "Keranjang, Checkout & Pembayaran")
    pdf.bullets([
        "FR-4.6.1 Keranjang: tambah/hapus buku, cek duplikat (sudah dimiliki tidak bisa dibeli lagi), ringkasan subtotal.",
        "FR-4.6.2 Checkout: input **kode voucher**, toggle **gunakan poin**, rincian diskon, total akhir.",
        "FR-4.6.3 Pembayaran via **Midtrans Snap** (VA, e-wallet, kartu, QRIS) pada environment sandbox lalu production.",
        "FR-4.6.4 Webhook Midtrans: verifikasi signature, **idempoten** (tabel `payment_webhooks`), transaksi ganda tidak "
        "menggandakan royalti maupun poin.",
        "FR-4.6.5 Status pesanan: `pending / paid / failed / refunded`; halaman riwayat + detail kwitansi.",
        "FR-4.6.6 Setelah `paid`: buku otomatis masuk **Pustaka** pengguna dan langsung bisa dibaca.",
        "FR-4.6.7 Admin dapat memproses refund; royalti terkait dikurangi (debit ledger).",
        "FR-4.6.8 Notifikasi email kwitansi + notifikasi in-app.",
    ])

    pdf.h2("4.7", "Sistem Promo: Diskon, Voucher, Flash Sale, Poin")
    pdf.h3("a. Diskon Buku")
    pdf.bullets([
        "FR-4.7.1 Penulis/penerbit mengatur `discount_price` beserta `discount_starts_at` dan `discount_ends_at` per buku.",
        "FR-4.7.2 Harga coret otomatis tampil di kartu & detail; berlaku otomatis saat jadwal aktif dan kembali normal saat selesai.",
        "FR-4.7.3 Diskon tidak boleh lebih tinggi dari batas konfigurasi admin (mis. maksimal 80%).",
    ])
    pdf.h3("b. Voucher")
    pdf.bullets([
        "FR-4.7.4 Bentuk: **persen** atau **nominal (flat)**; opsi minimal belanja, kuota pemakaian, masa berlaku.",
        "FR-4.7.5 Cakupan: **global** (dibuat admin), **penerbit** (berlaku untuk buku imprint/mitra), atau **per buku**.",
        "FR-4.7.6 Validasi saat checkout: kuota, masa berlaku, cakupan, kelayakan keranjang; pemakaian tercatat "
        "(`voucher_usages`) dengan penguncian agar kuota tidak melebihi batas.",
        "FR-4.7.7 Penulis biasa tidak membuat voucher (hanya diskon per buku).",
    ])
    pdf.h3("c. Flash Sale")
    pdf.bullets([
        "FR-4.7.8 Admin membuat kampanye: nama, jendela waktu (mulai/selesai), daftar buku, harga sale per buku.",
        "FR-4.7.9 Section flash sale di landing page dengan **countdown**; otomatis nonaktif saat waktu habis; "
        "harga kembali normal.",
        "FR-4.7.10 Harga flash sale menjadi **harga efektif** tertinggi prioritas saat checkout dan saat menghitung royalti.",
        "FR-4.7.11 Opsi batas terjual per kampanye (stock limit) dan tampilan 'terjual X%'.",
    ])
    pdf.h3("d. Poin Reward")
    pdf.bullets([
        "FR-4.7.12 Poin diperoleh dari pembelian (persentase dari total), pencapaian streak, dan review buku.",
        "FR-4.7.13 Poin dapat ditukarkan sebagai potongan saat checkout (konversi IDR, batas maksimum pemakaian per transaksi).",
        "FR-4.7.14 Setiap mutasi (earn/spend/revert) tercatat pada `point_transactions`; saldo dapat dicairkan? "
        "**Tidak** — poin bersifat non-tunai dan hanya untuk potongan belanja.",
        "FR-4.7.15 Poin otomatis hangus saat pesanan di-refund.",
    ])

    pdf.h2("4.8", "Wishlist & Notifikasi")
    pdf.bullets([
        "FR-4.8.1 Wishlist: tombol simpan pada kartu & detail buku; halaman khusus; jumlah wishlist di header.",
        "FR-4.8.2 Notifikasi otomatis saat buku di wishlist sedang diskon/masuk flash sale.",
        "FR-4.8.3 Notifikasi in-app (bola lonceng) + email untuk: pembayaran berhasil, buku disetujui/ditolak (dengan alasan), "
        "royalti masuk, penarikan diproses, balasan komentar, promo dari penerbit yang diikuti.",
        "FR-4.8.4 Newsletter subscribe di landing page; admin dapat mengirim buletin massal (queue email).",
        "FR-4.8.5 Preferensi notifikasi per jenis dapat dimatikan pengguna.",
    ])

    pdf.h2("4.9", "Rating & Komentar")
    pdf.bullets([
        "FR-4.9.1 Rating bintang 1-5, satu per pengguna per buku (dapat diperbarui), rata-rata tampil di detail & kartu.",
        "FR-4.9.2 Komentar berthreading (balasan), edit/hapus komentar milik sendiri.",
        "FR-4.9.3 Laporkan komentar (abuse) -> antrean moderasi admin.",
        "FR-4.9.4 Admin dapat menyembunyikan/menghapus komentar; penulis/penerbit dapat melaporkan komentar di bukunya.",
        "FR-4.9.5 Beri rating hanya untuk pengguna yang memiliki buku (pembeli / pemilik).",
    ])

    pdf.h2("4.10", "Gamifikasi & Engagement")
    pdf.bullets([
        "FR-4.10.1 **Reading streak**: penghitung hari beruntun membaca; dipertahankan selama membaca hari itu; "
        "riwayat & rekor terpanjang di profil.",
        "FR-4.10.2 **Badge**: koleksi lencana dengan kriteria (buku pertama, 10 buku selesai, streak 7/30 hari, "
        "penulis pertama terbit, dst.) beserta halaman galeri badge.",
        "FR-4.10.3 **Rekomendasi**: untuk pengguna login berdasarkan kategori yang sering dibaca + best seller; "
        "untuk tamu berdasarkan best seller & kategori populer.",
        "FR-4.10.4 **Buku Gratis Bulanan**: super admin memilih 1 buku gratis per bulan; ditandai di katalog & landing page.",
        "FR-4.10.5 **Lanjutkan Membaca**: kartu progress di beranda pengguna.",
        "FR-4.10.6 Notifikasi promo terjadwal (flash sale, voucher baru) mendorong kunjungan berulang.",
    ])

    pdf.h2("4.11", "Royalti & Kemitraan Penerbit")
    pdf.bullets([
        "FR-4.11.1 Setiap buku memiliki pembagian hasil (`book_revenue_shares`) berupa persentase ke **penulis** dan "
        "**penerbit** (dan opsional komisi pemasaran penerbit).",
        "FR-4.11.2 **Kemitraan** (`partnerships`): penulis dan penerbit mengajukan kerja sama dengan persentase default "
        "(mis. 70/30) yang dapat disesuaikan per buku; **disetujui super admin** sebelum aktif.",
        "FR-4.11.3 Penerbit dapat memasarkan buku penulis (flag unggulan/boost) dan memperoleh komisi sesuai kesepakatan.",
        "FR-4.11.4 **Royalti dihitung dari harga efektif bersih** (setelah diskon/flash sale) per `order_item`.",
        "FR-4.11.5 Setiap perolehan tercatat pada `royalty_ledger` (kredit) — audit penuh; refund menghasilkan debit.",
        "FR-4.11.6 Dashboard royalti: saldo, riwayat per pesanan, grafik per periode untuk penulis & penerbit.",
        "FR-4.11.7 Pembulatan aman: pembagian memakai algoritma integer-safe agar total pembulatan selalu = jumlah harga jual.",
    ])

    pdf.h2("4.12", "Penarikan Dana")
    pdf.bullets([
        "FR-4.12.1 Penulis/penerbit mengajukan penarikan (minimum saldo tertentu, kelipatan tertentu).",
        "FR-4.12.2 Admin menyetujui (lalu menandai sudah dibayar) atau menolak dengan alasan.",
        "FR-4.12.3 Saldo = kredit ledger - penarikan disetujui - debit refund; semua pergerakan tercatat.",
        "FR-4.12.4 Riwayat penarikan dengan detail rekening (snapshot saat pengajuan).",
        "FR-4.12.5 Notifikasi email saat penarikan disetujui/ditolak.",
    ])

    pdf.h2("4.13", "Dashboard Super Admin")
    pdf.bullets([
        "FR-4.13.1 Statistik: GMV, jumlah pesanan, pengguna baru, buku baru, penarikan & kemitraan tertunda.",
        "FR-4.13.2 Moderasi buku: antrean `pending`, preview, setujui/tolak dengan alasan.",
        "FR-4.13.3 Manajemen pengguna: cari, ubah role, ban/unban, lihat detail pembelian.",
        "FR-4.13.4 Pengelolaan kategori & tag.",
        "FR-4.13.5 Pembuatan voucher global dan kampanye flash sale.",
        "FR-4.13.6 Persetujuan kemitraan & penarikan dana; transaksi (detail, refund).",
        "FR-4.13.7 Site settings: logo, nama situs, persentase fee platform, nilai konversi poin, minimum penarikan, "
        "maksimum diskon, pengumuman/announcement bar.",
        "FR-4.13.8 Laporan/komentar yang dilaporkan (moderasi).",
    ])

    # ================= 5. ARSITEKTUR =================
    pdf.h1(5, "Arsitektur & Teknologi")
    pdf.h2("5.1", "Stack Teknologi")
    pdf.table(
        ["Layer", "Teknologi", "Catatan"],
        [
            ["Backend", "Laravel 13 (PHP >= 8.3)", "Framework utama; Sanctum/Breeze auth; Spatie Permission untuk RBAC"],
            ["Frontend", "Vue 3 + Inertia.js + Tailwind CSS", "SPA-style tanpa REST API terpisah; siap disambung API untuk mobile"],
            ["Database", "MySQL 8", "FULLTEXT search, transaksi ACID untuk checkout/royalti"],
            ["Cache & Queue", "Redis", "Antrean email, kalkulasi royalti, cache katalog, hitung mundur flash sale"],
            ["Object Storage", "Cloudflare R2 (S3-compatible)", "Seluruh akses **server-side**; file buku privat + temporary URL"],
            ["Payment", "Midtrans Snap", "Sandbox -> production; VA, e-wallet, kartu, QRIS"],
            ["Reader", "epub.js + pdf.js", "Render di browser; posisi CFI/halaman ke `reading_progress`"],
            ["Auth", "Laravel Breeze (Vue)", "Registrasi umum + halaman khusus `/daftar-penerbit`"],
        ],
        widths=(30, 56, 88),
    )
    pdf.h2("5.2", "Struktur Proyek")
    pdf.code(
        "bookreader/\n"
        "+-- app/\n"
        "|   +-- Enums/            BookStatus, OrderStatus, PartnershipStatus, ...\n"
        "|   +-- Http/Controllers/ User/ Author/ Publisher/ Admin/ Auth/\n"
        "|   +-- Http/Middleware/  EnsureRole, EnsureBookAccessible\n"
        "|   +-- Models/           User, Book, Order, Voucher, FlashSale, Partnership,\n"
        "|   |                     RoyaltyLedger, PointTransaction, Wishlist, ... \n"
        "|   +-- Policies/         BookPolicy, WithdrawalPolicy, PartnershipPolicy\n"
        "|   +-- Services/\n"
        "|   |   +-- Storage/BookStorage.php        upload R2, temporaryUrl, hapus\n"
        "|   |   +-- Payment/MidtransService.php    Snap + verifikasi webhook\n"
        "|   |   +-- Pricing/PriceResolver.php      harga efektif (flash/diskon)\n"
        "|   |   +-- Pricing/VoucherService.php     validasi & pemakaian voucher\n"
        "|   |   +-- Royalty/RoyaltyCalculator.php  pembagian integer-safe\n"
        "|   |   +-- Reward/PointService.php        earn/spend/hangus\n"
        "|   |   +-- Reader/ReaderAccessService.php izin akses buku\n"
        "|   +-- Jobs/             ProcessWebhook, SendReceipt, CalculateRoyalty,\n"
        "|                         NotifyWishlistDiscount, CloseFlashSale\n"
        "|   +-- Notifications/    OrderPaid, BookApproved, WithdrawalProcessed, ...\n"
        "+-- resources/js/\n"
        "|   +-- Pages/            Landing/ Store/ Reader/ Library/ Auth/ Author/\n"
        "|   |                     Publisher/ Admin/ Account/\n"
        "|   +-- Components/       BookCard, FlashSaleCountdown, RatingStars,\n"
        "|   |                     EpubViewer, PdfViewer, VoucherInput, ...\n"
        "|   +-- Layouts/          GuestLayout, StoreLayout, DashboardLayout\n"
        "+-- config/filesystems.php   disk 'r2'\n"
        "+-- database/migrations/\n"
        "+-- routes/web.php\n"
        "+-- tests/Feature/            checkout, webhook, royalti, voucher"
    )
    pdf.h2("5.3", "Storage Server-Side (Cloudflare R2)")
    pdf.para(
        "Seluruh interaksi dengan R2 terjadi di server Laravel. **Tidak ada kredensial, endpoint, atau S3 SDK di browser.**"
    )
    pdf.h3("Upload (buku & cover)")
    pdf.numbered([
        "Vue mengirim file (multipart POST) ke controller melalui XHR — progress bar ditampilkan dari sisi klien.",
        "Controller memvalidasi: tipe file, ekstensi, **magic bytes**, ukuran maksimum, dan (untuk EPUB) struktur arsip.",
        "File ditulis ke R2 via `Storage::disk('r2')->put(...)`; `s3_key` disimpan ke database.",
        "Cover disimpan terpisah dan disajikan lewat custom domain bucket (publik) atau proxy controller.",
    ])
    pdf.h3("Akses baca/download")
    pdf.numbered([
        "Reader meminta izin akses ke controller (cek pembelian / buku gratis / pemilik / admin).",
        "Server membuat **temporary URL** (`Storage::disk('r2')->temporaryUrl(...)`, berlaku mis. 5 menit).",
        "URL dikirim ke halaman reader; epub.js/pdf.js memuat file dari URL tersebut.",
        "URL kedaluwarsa tidak dapat dibagikan; setiap akses dikontrol ulang oleh sesi pengguna.",
    ])
    pdf.h3("Konfigurasi (hanya di server)")
    pdf.code(
        "// config/filesystems.php\n"
        "'disks' => [\n"
        "    'r2' => [\n"
        "        'driver' => 's3',\n"
        "        'key'    => env('R2_ACCESS_KEY_ID'),\n"
        "        'secret' => env('R2_SECRET_ACCESS_KEY'),\n"
        "        'bucket' => env('R2_BUCKET'),\n"
        "        'endpoint' => env('R2_ENDPOINT'), // https://<account_id>.r2.cloudflarestorage.com\n"
        "        'region'  => 'auto',\n"
        "        'use_path_style_endpoint' => false,\n"
        "        'throw' => true,\n"
        "        // tanpa 'visibility' => 'public' (R2 menolak ACL per-object)\n"
        "    ],\n"
        "],"
    )
    pdf.h2("5.4", "Keamanan")
    pdf.bullets([
        "**RBAC** — 4 role via Spatie Permission + middleware `EnsureRole` + Policy per resource.",
        "**Webhook payment** — verifikasi signature Midtrans + tabel `payment_webhooks` untuk idempotensi.",
        "**File privat** — temporary URL singkat; validasi magic bytes mencegah upload file berbahaya.",
        "**Transaksi DB** — checkout, pemakaian voucher, dan pencatatan royalti dalam satu transaksi atomik.",
        "**Rate limiting** — login, registrasi, dan endpoint checkout.",
        "**Kata sandi** hashing (bcrypt/argon2); proteksi CSRF/request-forgery bawaan Laravel 13.",
        "**Anti double-spend voucher/poin** — row lock (`lockForUpdate`) pada pemakaian.",
        "**Otorisasi berlapis** — akses data penulis hanya untuk pemilik (policy), bukan berdasar ID semata.",
    ])

    # ================= 6. MODEL DATA =================
    pdf.h1(6, "Model Data")
    pdf.para("Skema ringkas (field utama; relasi & index mengikuti praktik Laravel):")
    pdf.h2("6.1", "Identitas & Role")
    pdf.table(
        ["Tabel", "Field Utama", "Catatan"],
        [
            ["users", "id, name, email, password, role (user|author|publisher|admin), status (active|banned), avatar, "
                      "bank_name, bank_account_no, bank_holder", "Role default 'user'; upgrade ke author dari profil"],
            ["author_profiles", "user_id, bio, website, social_links(json)", "Opsional, untuk halaman publik penulis"],
            ["publisher_profiles", "user_id, imprint_name, legal_name, address, phone, npwp (opsional)",
             "Diisi saat /daftar-penerbit; langsung aktif"],
        ],
        widths=(34, 92, 48),
    )
    pdf.h2("6.2", "Katalog")
    pdf.table(
        ["Tabel", "Field Utama", "Catatan"],
        [
            ["books", "id, author_id, publisher_id?, title, slug, description, language, price, "
                      "discount_price, discount_starts_at, discount_ends_at, status (draft|pending|published|rejected|unpublished), "
                      "rejection_reason, is_free_monthly, featured_at, published_at",
             "FULLTEXT(title, description)"],
            ["book_files", "book_id, type (epub|pdf), s3_key, size_bytes, checksum", "Privat di R2"],
            ["book_covers", "book_id, s3_key, variant (thumb|full)", "Custom domain R2"],
            ["categories", "id, name, slug, parent_id?, icon", "Hierarki opsional 1 level"],
            ["category_book", "book_id, category_id", "Pivot"],
            ["ratings", "user_id, book_id, stars (1-5)", "UNIQUE(user_id, book_id)"],
            ["comments", "id, book_id, user_id, parent_id?, body, status (visible|hidden|deleted)",
             "Threaded, bisa dilaporkan"],
            ["comment_reports", "comment_id, reporter_id, reason, status", "Antrean moderasi admin"],
        ],
        widths=(34, 92, 48),
    )
    pdf.h2("6.3", "Promo & Engagement")
    pdf.table(
        ["Tabel", "Field Utama", "Catatan"],
        [
            ["vouchers", "id, code (unique), type (percent|fixed), value, min_spend, max_discount, quota, used_count, "
                         "scope (global|publisher|book), scope_id?, starts_at, expires_at, status, creator_type, creator_id",
             "Global dibuat admin; publisher untuk bukunya"],
            ["voucher_usages", "voucher_id, user_id, order_id, discount_amount", "Audit pemakaian"],
            ["flash_sales", "id, name, starts_at, ends_at, status, banner_text", "Kampanye admin"],
            ["flash_sale_items", "flash_sale_id, book_id, sale_price, sold_count, stock_limit?",
             "Harga efektif prioritas tertinggi"],
            ["wishlists", "user_id, book_id, created_at", "UNIQUE(user_id, book_id)"],
            ["point_transactions", "user_id, type (earn|spend|revert), points, balance_after, source (order|streak|review), "
                                    "reference_id?, note", "Ledger mutasi poin"],
            ["reading_streaks", "user_id, current_streak, longest_streak, last_read_date", "Diperbarui saat membaca"],
            ["badges", "code, name, description, icon, criteria (json)"],
            ["user_badges", "user_id, badge_id, earned_at", "UNIQUE"],
            ["newsletter_subscribers", "email, subscribed_at, token_unsub"],
        ],
        widths=(36, 92, 46),
    )
    pdf.h2("6.4", "Transaksi")
    pdf.table(
        ["Tabel", "Field Utama", "Catatan"],
        [
            ["carts / cart_items", "user_id / (cart_id, book_id, price_snapshot)", "Snapshot harga saat masuk keranjang (informatif)"],
            ["orders", "id, user_id, subtotal, voucher_discount, point_discount, total, status (pending|paid|failed|refunded), "
                       "midtrans_order_id, snap_token, voucher_id?, points_used, paid_at",
             "Satu order banyak item"],
            ["order_items", "order_id, book_id, price (harga efektif), discount_source (none|diskon|flash), "
                            "author_share, publisher_share", "Snapshot final untuk royalti"],
            ["payment_webhooks", "midtrans_order_id, event_type, payload (json), signature, processed_at",
             "Idempotensi webhook"],
            ["refunds", "order_id, amount, reason, approved_by, created_at", "Oleh admin"],
        ],
        widths=(38, 90, 46),
    )
    pdf.h2("6.5", "Royalti, Kemitraan & Dana")
    pdf.table(
        ["Tabel", "Field Utama", "Catatan"],
        [
            ["partnerships", "id, author_id, publisher_id, status (pending|active|ended), default_author_pct, "
                             "default_publisher_pct, marketing_pct, approved_by?, proposed_by", "Butuh approval admin"],
            ["book_revenue_shares", "book_id, payee_type (author|publisher), payee_id, percentage",
             "Default dari partnership; override per buku"],
            ["royalty_ledger", "id, payee_type, payee_id, book_id?, order_item_id?, type (credit|debit), amount, "
                               "balance_after, note", "Audit penuh, sumber saldo"],
            ["withdrawals", "user_id, amount, status (pending|approved|rejected|paid), bank_snapshot (json), "
                            "processed_by, processed_at, reject_reason", "Minimum & kelipatan dari settings"],
            ["notifications", "Bawaan Laravel (database driver)", "In-app lonceng + email"],
            ["settings", "key, value", "Site config: fee%, konversi poin, min withdrawal, dll."],
            ["activity_logs", "user_id, action, subject_type, subject_id, meta (json)", "Jejak aksi admin"],
        ],
        widths=(34, 94, 46),
    )

    # ================= 7. ALUR =================
    pdf.h1(7, "Alur Kritis")
    pdf.h2("7.1", "Resolusi Harga (Promo)")
    pdf.numbered([
        "Ambil harga dasar `books.price`.",
        "Jika ada **flash sale aktif** untuk buku itu -> pakai `flash_sale_items.sale_price`.",
        "Selain itu jika **diskon terjadwal aktif** (`discount_price` dalam jadwal) -> pakai `discount_price`.",
        "Hasilnya = **harga efektif** (ditampilkan di UI sebagai harga jual; harga dasar tampil tercoret).",
        "**Voucher** dan **poin** dihitung dari subtotal harga efektif keranjang, bukan dari harga dasar.",
    ])
    pdf.h2("7.2", "Checkout & Pembagian Royalti")
    pdf.numbered([
        "User menekan Checkout -> server memvalidasi ulang keranjang, voucher, dan poin (harga dihitung ulang server-side).",
        "Order dibuat `pending`; Snap token diminta ke Midtrans; pemakaian voucher & poin dicatat sementara (dengan lock).",
        "User membayar di Snap -> Midtrans mengirim webhook.",
        "Webhook diverifikasi signature-nya; payload disimpan ke `payment_webhooks` (jika sudah ada -> lewati, idempoten).",
        "Order menjadi `paid`; `order_items` menyimpan harga efektif per item.",
        "`RoyaltyCalculator` membaca `book_revenue_shares` -> menulis `royalty_ledger` credit untuk penulis & penerbit "
        "(integer-safe, total selalu = harga jual).",
        "Poin diberikan ke pembeli; voucher quota dinaikkan permanen; email kwitansi terkirim (queue).",
        "Buku otomatis masuk Pustaka; notifikasi in-app ke pembeli, penulis, dan penerbit.",
    ])
    pdf.h2("7.3", "Kemitraan Penerbit <-> Penulis")
    pdf.numbered([
        "Pihak mana pun mengajukan kemitraan: memilih mitra, persentase default, dan ruang lingkup.",
        "Super admin meninjau -> **approve** (status `active`) atau tolak dengan alasan.",
        "`book_revenue_shares` dibuat/diperbarui untuk buku tercakup.",
        "Setiap penjualan buku tercakup otomatis terbagi sesuai persentase dan tercatat di ledger.",
        "Penerbit dapat menandai buku mitra sebagai unggulan (pemasaran); komisi pemasaran opsional ikut dihitung.",
    ])
    pdf.h2("7.4", "Upload & Moderasi Buku")
    pdf.numbered([
        "Penulis/penerbit (role aktif seketika setelah daftar) membuat buku -> mengunggah file & cover (server-side ke R2).",
        "Status menjadi `pending`; buku masuk antrean moderasi admin.",
        "Admin meninjau -> `published` (tampil di katalog) atau `rejected` + alasan (penulis memperbaiki & mengajukan ulang).",
        "Saat `published`, pemilik dapat mengubah konten; perubahan harga/ promo langsung berlaku; "
        "perubahan file utama memicu moderasi ulang (opsional, konfigurasi).",
    ])
    pdf.h2("7.5", "Akses Reader")
    pdf.numbered([
        "Pengguna membuka `/baca/{book}` -> `ReaderAccessService` mengecek: pemilik, pembeli (order paid), buku gratis/sampel, atau admin.",
        "Jika lolos -> temporary URL dibuat server-side -> reader memuat konten.",
        "Progres dikirim berkala (debounce) ke `reading_progress`; membaca hari ini memperbarui streak & mungkin memicu badge.",
        "Jika tidak berwenang -> redirect ke halaman detail untuk membeli.",
    ])
    pdf.h2("7.6", "Penarikan Dana")
    pdf.numbered([
        "Penulis/penerbit mengajukan penarikan (saldo >= minimum).",
        "Admin menyetujui -> status `approved` -> menandai `paid` (transfer manual) -> ledger debit dicatat.",
        "Atau ditolak dengan alasan -> dana kembali tersedia.",
    ])

    # ================= 8. RUTE =================
    pdf.h1(8, "Peta Halaman & Rute")
    pdf.table(
        ["Grup", "Rute", "Akses", "Keterangan"],
        [
            ["Publik", "/ , /buku, /buku/{slug}, /kategori/{slug}, /penulis/{slug}, /penerbit/{slug}", "Tamu + login",
             "Landing page & katalog"],
            ["Auth", "/login, /register, /lupa-sand", "Tamu", "Registrasi umum: pembaca/penulis"],
            ["Auth Penerbit", "/daftar-penerbit", "Tamu", "Halaman khusus pendaftaran penerbit"],
            ["Pembaca", "/pustaka, /baca/{book}, /keranjang, /checkout, /pesanan, /pesanan/{id}", "user+",
             "Koleksi, reader, transaksi"],
            ["Akun", "/profil, /wishlist, /poin, /badge, /notifikasi, /pengaturan-notifikasi", "login", "Preferensi & reward"],
            ["Penulis", "/author (dashboard), /author/buku, /author/buku/baru, /author/buku/{id}/edit, "
                        "/author/penjualan, /author/royalti, /author/penarikan, /author/kemitraan", "author+",
             "Kelola buku & penghasilan"],
            ["Penerbit", "/publisher/** (subset author) + /publisher/mitra, /publisher/kemitraan, "
                         "/publisher/voucher, /publisher/katalog-imprint", "publisher", "Fitur penulis + kemitraan & voucher"],
            ["Admin", "/admin (dashboard), /admin/moderasi, /admin/pengguna, /admin/kategori, /admin/voucher, "
                      "/admin/flash-sale, /admin/kemitraan, /admin/penarikan, /admin/transaksi, /admin/komentar, "
                      "/admin/pengaturan", "admin", "Kontrol penuh"],
            ["Webhook", "/midtrans/callback (POST)", "Midtrans", "Server-to-server, verifikasi signature"],
        ],
        widths=(24, 66, 24, 60),
    )

    # ================= 9. NON-FUNGSIONAL =================
    pdf.h1(9, "Persyaratan Non-Fungional")
    pdf.table(
        ["Kategori", "Persyaratan"],
        [
            ["Performa", "Muat halaman katalog < 2,5s (p50) dengan cache Redis; cover di-lazy load & di-optimalkan; "
                         "berat (hitung royalti, kirim email) via queue; pencarian FULLTEXT terindeks."],
            ["Skalabilitas", "Stateless app + Redis queue memungkinkan horizontal scaling; R2 menangani storage tanpa "
                             "biaya egress."],
            ["Keamanan", "OWASP Top 10; rate limit auth; signed URL singkat; webhook terverifikasi; audit log admin; "
                         "backup database terjadwal."],
            ["Ketersediaan", "Target 99,9% untuk jalur baca & pembayaran; retry queue untuk notifikasi."],
            ["SEO", "Meta title/description per halaman, Open Graph, sitemap.xml, struktur heading semantik; "
                    "Inertia SSR opsional untuk halaman publik."],
            ["Responsive", "Mobile-first: katalog, reader, dan checkout nyaman di layar kecil (reader mendukung "
                           "mode potret/landscape)."],
            ["Aksesibilitas", "Kontras teks memadai, navigasi keyboard dasar pada reader, alt text cover."],
            ["Kompatibilitas", "Chrome, Firefox, Safari, Edge versi terbaru 2 tahun; epub.js & pdf.js modern."],
            ["Observability", "Log aplikasi terpusat, error tracking (mis. Sentry), dasbor admin real-time (queue)."],
            ["Backup & Retensi", "Backup MySQL harian; R2 lifecycle/versioning opsional untuk file penting; "
                                 "retensi log webhook."],
        ],
        widths=(36, 138),
    )

    # ================= 10. ROADMAP =================
    pdf.h1(10, "Peta Jalan Implementasi (10 Fase)")
    pdf.table(
        ["Fase", "Nama", "Cakupan", "Keluaran (Definition of Done)"],
        [
            ["1", "Fondasi", "Proyek Laravel 13 + Inertia Vue + Tailwind, Breeze auth, 4 role & middleware, disk R2 "
                             "server-side + BookStorage service, struktur folder, seeders dasar",
             "Login 4 role berfungsi; upload contoh ke R2 berhasil via endpoint"],
            ["2", "Landing & Katalog", "Landing page (hero, flash sale placeholder, koleksi, statistik, newsletter), "
                                        "katalog + filter/search/sort, detail buku, kategori, wishlist",
             "Tamu dapat menjelajah & menyimpan wishlist"],
            ["3", "Upload Buku", "CRUD buku penulis/penerbit, upload EPUB/PDF & cover server-side, status pending, "
                                 "halaman publik penulis/penerbit",
             "Buku baru masuk antrean moderasi dan tampil bila disetujui"],
            ["4", "Reader", "epub.js + pdf.js, temporary URL, progres, bookmark, highlight, preferensi baca, sampel gratis",
             "Pembeli dapat membaca penuh; progres tersimpan lintas perangkat"],
            ["5", "Transaksi & Promo Dasar", "Keranjang, voucher & poin di checkout, Midtrans Snap + webhook idempoten, "
                                             "riwayat pesanan, pustaka otomatis",
             "Alur beli end-to-end di sandbox Midtrans"],
            ["6", "Flash Sale & Diskon", "Diskon terjadwal, kampanye flash sale + countdown di landing, "
                                          "notifikasi wishlist diskon",
             "Harga efektif teriterasi benar di katalog & checkout"],
            ["7", "Royalti & Kemitraan", "PriceResolver -> RoyaltyCalculator, ledger, kemitraan + approval, "
                                          "withdrawal flow, dashboard earnings",
             "Setiap order paid terbagi tepat ke penulis & penerbit"],
            ["8", "Sosial & Gamifikasi", "Rating, komentar threading + moderasi, streak, badge, rekomendasi, "
                                          "buku gratis bulanan, lanjutkan membaca",
             "Fitur engagement aktif & terukur"],
            ["9", "Admin", "Dashboard statistik, moderasi, manajemen user/kategori, kelola voucher & flash sale, "
                           "approve kemitraan/penarikan, transaksi & refund, site settings",
             "Super admin mengatur seluruh platform tanpa akses DB langsung"],
            ["10", "Notifikasi & Poles", "Email + in-app notification center, newsletter blast, SEO meta & sitemap, "
                                         "seed data demo, test suite, optimasi performa",
             "Siap demo end-to-end & siap deploy production"],
        ],
        widths=(12, 34, 74, 54),
    )
    pdf.para(
        "Setiap fase diakhiri dengan verifikasi manual alur utama dan test yang relevan sebelum fase berikutnya dimulai. "
        "Urutan dapat menyesuaikan, namun ketergantungan teknis (mis. promo butuh transaksi) harus dijaga."
    )

    # ================= 11. ASMUMSI =================
    pdf.h1(11, "Asumsi, Batasan & Out of Scope")
    pdf.h2("11.1", "Asumsi")
    pdf.bullets([
        "Registrasi **penulis** melalui halaman umum `/register`; **penerbit** melalui `/daftar-penerbit` — keduanya "
        "**langsung aktif tanpa verifikasi admin**.",
        "Persetujuan admin hanya berlaku untuk **penarikan dana** dan **kemitraan** (bagi hasil) karena menyangkut uang.",
        "Mata uang transaksi **IDR**; bahasa antarmuka **Bahasa Indonesia** (struktur i18n disiapkan agar dapat dikembangkan).",
        "Poin reward **non-tunai** — hanya dapat dipotongkan saat checkout.",
        "Pembayaran digital sepenuhnya via **Midtrans**; transfer bank manual (topup) tidak tersedia.",
        "Penerbit boleh membuat voucher; penulis cukup dengan diskon per buku.",
        "Semua file buku & cover disimpan di **Cloudflare R2** dengan akses **server-side** penuh.",
    ])
    pdf.h2("11.2", "Out of Scope (versi 1.0)")
    pdf.bullets([
        "Aplikasi mobile native (iOS/Android) — API dapat disiapkan di fase lanjutan.",
        "Buku audio (audiobook) dan video.",
        "DRM berat / enkripsi konten per pengguna (dilindungi temporary URL & kontrol akses).",
        "Multi-bahasa UI (i18n lengkap) dan multi-mata uang.",
        "Program afiliasi eksternal & integrasi marketplace pihak ketiga.",
        "Langganan/bulanan (subscription) — model per-beli; dapat ditambahkan kemudian.",
        "Editor buku berbasis web (penulis menulis konten langsung di platform) — fokus file EPUB/PDF.",
    ])

    # ================= 12. RISIKO =================
    pdf.h1(12, "Risiko & Mitigasi")
    pdf.table(
        ["Risiko", "Dampak", "Mitigasi"],
        [
            ["Webhook Midtrans terlewat / duplikat", "Status order & royalti salah",
             "Tabel idempoten + verifikasi signature + scheduler rekonsiliasi status order via Midtrans API"],
            ["Pembulatan pembagian royalti tidak persis", "Selisih saldo antar payee",
             "Algoritma integer-safe (largest remainder) + test unit pada kasus bulat/ganjil"],
            ["File buku dibagikan via link", "Kerugian penulis/penerbit",
             "Temporary URL singkat, kontrol izin per request, opsional watermark pembeli pada PDF"],
            ["Lonjakan traffic flash sale", "Lambat / gagal checkout",
             "Cache harga & section landing, queue untuk pekerjaan berat, rate limit checkout"],
            ["Backlog moderasi buku", "Penulis menunggu lama",
             "Notifikasi admin real-time, SLA internal, prioritas antrean"],
            ["Kuota voucher dipakai bersamaan melebihi batas", "Kerugian promosi",
             "Row lock `lockForUpdate` saat pemakaian + pengecekan atomik"],
            ["Salah hitung harga efektif (diskon vs flash)", "Komplain pembeli",
             "Satu PriceResolver terpusat yang dipakai katalog, checkout, dan royalti"],
        ],
        widths=(44, 44, 86),
    )

    # penutup
    pdf.ln(4)
    pdf.set_fill_color(*LIGHT)
    pdf.set_draw_color(*ACCENT)
    pdf.set_font("DejaVu", "", 9)
    pdf.set_text_color(*DARK)
    pdf.multi_cell(
        USABLE,
        5.2,
        "  Dokumen ini adalah acuan tunggal (single source of truth) untuk pengembangan PerpusKita. "
        "Setiap perubahan lingkup wajib memperbarui versi PRD ini beserta nomor FR terdampak. "
        "Disetujui untuk diimplementasikan sesuai peta jalan 10 fase.",
        fill=True,
        border="L",
    )

    pdf.output("/root/bookreader/prd.pdf")
    print("OK -> /root/bookreader/prd.pdf")


if __name__ == "__main__":
    build()
