# Design System & UI/UX Specification: Kee Laundry (Modern Responsive & Native Mobile App Feel)

## 1. Filosofi Desain: Clean Blue-White & Native Mobile First
- **Identitas Visual:** *Hygienic White, Crisp Sky Blue, & Fresh Aqua*. Memberikan kesan bersih, wangi, modern, dan profesional seperti aplikasi *fintech* atau *on-demand laundry app* masa kini.
- **Prinsip Responsif (Adaptive Multi-Device Architecture):**
  Aplikasi tidak sekadar "mengecilkan" tampilan desktop ke layar HP, melainkan bertransformasi sesuai perangkat yang digunakan:
  1. **Mobile View (`< 768px` - Smartphone Owner, Operator Produksi, Kurir, Pelanggan):**
     - Menggunakan **Fixed Bottom Navigation Bar** (5 menu utama dengan ikon + indikator aktif) dan **Floating Action Button (FAB)** `+ Order` di tengah/kanan bawah.
     - Semua form CRUD (Tambah/Edit Pelanggan, Edit Layanan, Input Listrik/Air, Stok Opname) muncul sebagai **Slide-Up Bottom Sheet Drawer** dengan *drag handle* di bagian atas, persis seperti aplikasi iOS/Android modern.
     - Semua tabel data panjang otomatis berubah menjadi **Interactive Touch Cards** dengan *action chips* yang nyaman ditekan satu jempol (minimal ukuran *touch target* $44 \times 44\text{ px}$).
  2. **Tablet & Desktop View (`>= 768px` - Kasir POS & Admin Backoffice):**
     - Menggunakan **Collapsible Left Sidebar**, **Split-Screen POS Layout**, dan **Multi-Column Kanban Board**.

---

## 2. Color Palette & Surface Tokens (Tailwind CSS)

### A. Surfaces & Glassmorphism
- **App Canvas Background:** `#F8FAFC` (`bg-slate-50`)
- **Card / Surface Primary:** `#FFFFFF` (`bg-white`) dengan border `#E2E8F0` (`border-slate-200/80`)
- **Mobile Topbar & Bottom Bar Surface:** `bg-white/90 backdrop-blur-md` (Efek kaca buram modern saat halaman di-scroll)
- **Active Selection Surface:** `#F0F9FF` (`bg-sky-50`) dengan border `#7DD3FC` (`border-sky-300`)

### B. Brand & Semantic Colors
- **Kee Primary Blue:** `#0284C7` (`bg-sky-600`, `text-sky-600`) — Warna tombol utama & elemen aktif.
- **Fresh Cyan Accent:** `#06B6D4` (`bg-cyan-500`) — Aksen visual air, tangki cairan, dan badge express.
- **Emerald (Lunas / Selesai / Stok Aman):** `#10B981` (`bg-emerald-50 text-emerald-700 border-emerald-200`)
- **Amber (Proses / Stok Menipis):** `#F59E0B` (`bg-amber-50 text-amber-700 border-amber-200`)
- **Rose (Belum Lunas / Indikasi Boros Sabun / Void):** `#F43F5E` (`bg-rose-50 text-rose-700 border-rose-200`)

---

## 3. Arsitektur Navigasi Responsif (Desktop vs Mobile App)

### A. Mobile App Shell (`< 768px`)
1. **Sticky Mobile App Header (`h-14`):**
   - Kiri: Logo **Kee Laundry** + *Branch Pill Selector* (`Cabang Utama ▾` — siap untuk multi-cabang).
   - Kanan: Badge ringkas `HPP: Rp 1.750/Kg` + Tombol Menu Lainnya (Drawer untuk Master Layanan, HR, & Cek Nota).
2. **Fixed Bottom Navigation Bar (`h-16 fixed bottom-0 inset-x-0 z-40`):**
   - Terdiri dari 5 tab ikonik:
     1. `Beranda` (Dashboard & HPP)
     2. `Produksi` (Kanban & Rak)
     3. **Tombol Tengah Menonjol (Elevated Primary Button):** `+ Kasir` (Buka POS Cepat)
     4. `CRM` (Pelanggan & Deposit)
     5. `Gudang` (Sabun, Listrik & HR)
   - Dilengkapi `pb-20` pada *main content container* agar konten paling bawah tidak tertutup oleh Bottom Bar.

### B. Desktop / Tablet App Shell (`>= 768px`)
- **Left Sidebar (`w-64`):** Menampilkan seluruh modul secara vertikal + ringkasan status cabang aktif di bagian bawah sidebar.
- **Top Header (`h-16`):** Menampilkan *Branch Switcher*, indikator mesin aktif, pencarian global nota, dan tombol cepat `+ Transaksi Baru`.

---

## 4. Desain Komponen Mobile-First & Full CRUD

### 1. Mobile POS & Sticky Checkout Bar
- Di layar Desktop, keranjang kasir berada di kolom kanan (40%).
- Di layar Mobile (`< 768px`), katalog layanan tampil penuh 1 kolom/2 kolom kompak, sementara ringkasan tagihan berubah menjadi **Floating Bottom Checkout Bar** di atas navigasi bawah:
  - Menampilkan `Total: Rp 36.000 (4.5 Kg)` + info kecil `Auto-deduct: 67.5ml Sabun`.
  - Ketika diklik, membuka **Bottom Sheet Ringkasan Order & Pembayaran** penuh untuk memilih parfum, metode bayar, dan cetak struk.

### 2. Mobile Kanban dengan Swipeable Stage Pills
- Di layar Mobile, operator cuci/setrika tidak perlu menggeser 5 kolom sempit.
- Di bagian atas terdapat **Horizontal Scrollable Stage Pills** dengan badge angka:
  `[Antrean (3)]` `[Cuci (2)]` `[Setrika (4)]` `[Packing (1)]` `[Siap Ambil (5)]`
- Memilih salah satu tab akan menampilkan daftar kartu nota secara vertikal yang lega, lengkap dengan tombol besar **"Update Status & Klaim Komisi"** dan pemilih **Lokasi Rak**.

### 3. Responsive Data View: Tabel Desktop $\leftrightarrow$ Kartu Mobile
Untuk seluruh halaman CRUD (Riwayat Order, Pelanggan CRM, Karyawan HR, Stok Sabun, Log Listrik/Air):
- **Pada layar `md:` ke atas:** Tampil sebagai tabel bersih dengan kolom lengkap dan tombol aksi ikon (`Edit`, `Delete`, `Detail`).
- **Pada layar `< md:` (Mobile):** Otomatis dirender sebagai **Modern Card List** bersudut membulat (`rounded-2xl p-4 shadow-sm border border-slate-200/80`) yang memuat:
  - Baris Atas: Judul/Nama + Badge Status/Tier.
  - Baris Tengah: Grid 2-3 metrik kunci (misal: Sisa Deposit Kg, No WA, atau Sisa Liter Sabun).
  - Baris Bawah: Barisan tombol aksi ramah jempol (`Edit Data`, `Top-Up / Opname`, `Hapus`).

### 4. Slide-Up Bottom Sheet Modal untuk Semua Form CRUD
- Semua aksi **Tambah Data (Create)** dan **Edit Data (Update)** menggunakan satu komponen modal adaptif:
  - Di Desktop: Tampil sebagai *Centered Modal Dialog* (`max-w-lg rounded-2xl`).
  - Di Mobile: Tampil menempel di bagian bawah layar (`fixed bottom-0 inset-x-0 rounded-t-3xl max-h-[90vh] overflow-y-auto`) lengkap dengan garis *handle* abu-abu (`w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-4`) untuk memberikan sensasi aplikasi mobile *native* terbaru.

---

## 5. Standar Anti-AI SLOP UI (Strict Polish Standard)

Menghindari kesalahan umum desain "AI Slop" (layout kaku, tombol balok menumpuk, teks patah berantakan, desimal mubazir, dan dialog janggal):

### A. Mobile Layout Anti-Patterns & Solusi
1. **Dilarang Menumpuk 3 Tombol Balok Penuh:**
   - ❌ *AI Slop:* Tiga tombol besar bertingkat (`[Nonaktifkan]`, `[Edit]`, `[Hapus]`) di tiap kartu sehingga 2 kartu memenuhi seluruh layar HP.
   - ✅ *Solusi:* Gunakan tombol aksi kompak (`Edit` dengan icon pensil + toggle status), dan gunakan tombol icon sampah merah minimalis di pojok kanan.
2. **Interactive Status Badge (Quick Toggle):**
   - Jadikan badge status `✓ Aktif` / `Nonaktif` dapat diklik langsung (clickable chip) untuk mengaktifkan/menonaktifkan item secara instan tanpa perlu tombol balok terpisah.
3. **Navigasi Modal Bottom Sheet Bergaya Native:**
   - Tombol navigasi kembali menggunakan **Back (`←`)** di pojok kiri atas dekat judul/icon.
   - Hapus tombol Close `(X)` di kanan atas pada mobile dan **hilangkan tombol "Tutup" footer** di bagian bawah yang membuang area scroll vertikal.
4. **No Awkward Price Wrapping:**
   - Label harga dan satuan WAJIB dibungkus `whitespace-nowrap` (`Rp 10.000/Kg`), jangan sampai `/Kg` patah ke baris bawah.
5. **Clean Numbers & Typography:**
   - Hilangkan desimal `.00` pada angka bulat (misal `15ml`, bukan `15.00ml`).
   - Sembunyikan subtitle deskripsi panjang di header pada mobile (`hidden sm:block`) agar area scroll konten langsung luas.

### B. Desktop Layout Anti-Patterns & Solusi
1. **Dilarang Menyembunyikan Tabel Master di Balik Placeholder Kosong:**
   - ❌ *AI Slop:* Tabel master disembunyikan dan hanya menampilkan box dashed kosong bertuliskan "Klik salah satu kategori di atas...".
   - ✅ *Solusi:* Di desktop (`hidden md:block`), **Tabel Master Layanan WAJIB selalu tampil penuh**.
2. **Kartu Kategori Sebagai Quick Filter Tab:**
   - Kartu kategori di atas berfungsi sebagai **Interactive Quick Filter Tab**. Klik kartu kategori langsung memfilter tabel di bawahnya (diberi highlight border biru + indikator `✓ Aktif difilter`), klik sekali lagi untuk reset.
3. **Pemisahan Navigasi Desktop vs Mobile Modal:**
   - Tombol panah Back (`←`) khusus ditampilkan di mobile (`md:hidden`).
   - Di desktop, modal dialog centered tetap menggunakan tombol Close `(X)` di kanan atas yang proporsional.
