# Product Requirements Document (PRD): Kee Laundry ERP & POS (Full CRUD & Multi-Branch Ready)

## 1. Informasi & Visi Proyek
- **Nama Aplikasi:** Kee Laundry
- **Tech Stack Utama:** Laravel 11/12, Livewire 3, Alpine.js, Tailwind CSS, MySQL 8.0+
- **Skala & Arsitektur Cabang:**
  - **Saat Ini (Fase 1 - MVP Showcase):** Fokus operasional penuh pada **1 Toko Utama (`Kee Laundry - Cabang Utama`)**.
  - **Masa Depan (Fase 2 - Multi-Branch Scalable):** Seluruh tabel database sudah dilengkapi kolom `branch_id` dan *Global Scope* Laravel sehingga saat bisnis ekspansi ke cabang ke-2, ke-3, dst., sistem tidak perlu perombakan database (*Zero Refactoring*).
- **Standar Operasional Data:** Seluruh modul wajib memiliki kapabilitas **Full CRUD (Create, Read/Filter/Search, Update, Delete/Soft-Delete)** dengan validasi form *real-time* dan proteksi transaksi database (`DB::transaction`).
- **Standar Pengalaman Pengguna (UX):** *Adaptive Layout*—tampilan *Split-Screen / Dashboard* untuk Desktop & Tablet Kasir, serta tampilan **Native-Like Mobile App** (Bottom Bar, Swipeable Tabs, Bottom Sheet Modals) saat diakses dari smartphone oleh Owner, Karyawan Produksi, Kurir, maupun Pelanggan.

---

## 2. Hak Akses & Role Pengguna
1. **Super Admin / Owner:** Akses penuh seluruh CRUD, pengaturan cabang/toko, master layanan & resep BOM, opname stok, analitik HPP, dan persetujuan payroll.
2. **Kasir (Front Desk):** CRUD transaksi POS, CRUD pelanggan & top-up deposit, update rak pengambilan, dan input tiket komplain.
3. **Staf Produksi (Cuci, Setrika, Packing):** Update status Kanban cucian, klaim PIC produksi (otomatis masuk komisi), dan lihat riwayat komisi pribadi.
4. **Kurir Antar-Jemput:** Melihat antrean jemput/antar di tampilan mobile dan mengubah status pengiriman.
5. **Pelanggan (Public Mobile Web):** Melacak progres cucian via nomor nota/QR Code tanpa login.

---

## 3. Spesifikasi Fungsional & Matriks Full CRUD per Modul

### Modul 1: Pengaturan Toko & Cabang (`BranchSettingLivewire`) — *Multi-Branch Ready*
Meskipun fokus di 1 toko terlebih dahulu, profil toko tidak boleh *hardcoded* melainkan dikelola dari database:
- **Create:** Tambah cabang baru (disiapkan untuk ekspansi kedepan).
- **Read:** Tampilkan profil cabang aktif (`Kee Laundry - Cabang Utama`), alamat, nomor WhatsApp Admin, footer struk thermal, dan target HPP per Kg.
- **Update:** Edit nama cabang, logo, alamat, jam operasional, teks promo di struk, dan tarif dasar listrik/air lokal.
- **Delete:** Nonaktifkan cabang (`is_active = false`).

### Modul 2: Master Layanan & Resep Takaran Sabun (`ServicePackageCrudLivewire`)
Mengelola daftar harga cucian sekaligus rumus *Auto-Deduct* bahan baku (Bill of Materials):
- **Create:** Tambah paket layanan baru (Nama Layanan, Kategori `KILOAN`/`SATUAN`/`EXPRESS`, Harga per Unit, Satuan `Kg`/`Pcs`/`Meter`, Estimasi Jam Selesai, serta takaran otomatis: $\text{ml Deterjen/Unit}$, $\text{ml Softener/Unit}$, $\text{ml Parfum/Unit}$).
- **Read:** Daftar layanan dengan pencarian instan, filter per kategori, dan indikator modal bahan baku per layanan.
- **Update:** Edit harga layanan, ubah estimasi durasi, atau sesuaikan takaran ml sabun.
- **Delete:** *Soft-delete* atau nonaktifkan layanan lama tanpa merusak histori nota transaksi sebelumnya.

### Modul 3: Kasir POS & Manajemen Transaksi (`PosOrderCrudLivewire`)
- **Create (Input Order di POS):**
  - Pilih pelanggan dari dropdown pencarian atau klik **Quick Create Pelanggan Baru** langsung dari layar POS tanpa pindah halaman.
  - Munculkan *CRM Preference Banner* otomatis (misal: *"Parfum Sakura, jangan dilipat"*).
  - Pilih layanan, input berat desimal (contoh: $4.5\text{ Kg}$), pilih aroma parfum, catatan khusus, dan status pembayaran (`LUNAS` / `BELUM_LUNAS`).
  - Saat disimpan: Otomatis memotong stok ml sabun di gudang dan memotong saldo Deposit Kg pelanggan (jika bayar via deposit).
- **Read (Daftar & Riwayat Transaksi):**
  - Tabel & *Mobile Card List* seluruh transaksi dengan filter tanggal, status bayar, status produksi, dan metode pembayaran.
  - Preview Struk Thermal 58mm & kirim ulang nota via WhatsApp.
- **Update (Edit Order & Pembayaran):**
  - **Pelunasan Tagihan:** Ubah status `BELUM_LUNAS` menjadi `LUNAS` saat pelanggan mengambil cucian.
  - **Revisi Berat / Layanan:** Jika ada koreksi timbangan (misal dari $4\text{ Kg}$ menjadi $5\text{ Kg}$), sistem otomatis menghitung ulang selisih tagihan dan menyesuaikan potongan stok bahan baku.
- **Delete (Void / Pembatalan Nota):**
  - Batalkan transaksi (*Soft Delete* / Status `VOID`) disertai alasan pembatalan.
  - **Auto-Rollback:** Saat nota dibatalkan, sistem otomatis mengembalikan (*restore*) stok ml sabun ke gudang dan mengembalikan saldo Deposit Kg pelanggan.

### Modul 4: Produksi, Kanban Board & Smart Rack (`ProductionKanbanLivewire`)
- **Create / Assign Log Produksi:** Mencatat siapa karyawan yang mencuci (`washed_by_id`), menyetrika (`ironed_by_id`), dan melakukan QC/Packing (`packed_by_id`).
- **Read:**
  - **Desktop:** Papan Kanban 5 kolom (`Antrean` $\rightarrow$ `Cuci & Kering` $\rightarrow$ `Setrika` $\rightarrow$ `QC & Packing` $\rightarrow$ `Siap Diambil`).
  - **Mobile App View:** *Swipeable Segmented Tabs* per tahapan sehingga operator produksi cukup memakai HP di dekat mesin cuci/meja setrika.
- **Update:**
  - Pindahkan status cucian ke tahap berikutnya + klaim nama karyawan (otomatis menambah output $\text{Kg}$ dan komisi karyawan).
  - Pilih/ubah lokasi **Smart Rack** (contoh: `Rak A-03`) saat barang selesai dipacking.
  - Koreksi PIC karyawan jika terjadi salah klik (komisi karyawan lama dikurangi, komisi karyawan baru ditambah).
- **Delete:** Reset progres status kembali ke `Antrean` jika diperlukan pencucian ulang (*Re-wash*).

### Modul 5: CRM Pelanggan, Deposit Kuota & Komplain (`CustomerCrmCrudLivewire`)
- **Create:**
  - Tambah pelanggan baru (Nama, No. WA unik, Alamat jemput, Tier `REGULAR`/`SILVER`/`VIP`, Preferensi pencucian).
  - Buat transaksi **Top-Up Deposit Kuota Kg** (misal beli paket $50\text{ Kg}$).
  - Buat tiket **Komplain Pelanggan** baru terkait nomor nota tertentu.
- **Read:**
  - Direktori pelanggan lengkap dengan total transaksi, sisa deposit Kg, poin loyalitas, dan tab khusus **Churn Alert** (pelanggan pasif $> 14$ hari belum mencuci).
- **Update:**
  - Edit profil & preferensi pelanggan, upgrade Tier member, sesuaikan saldo deposit secara manual (dengan catatan log), dan update status penyelesaian komplain (`OPEN` $\rightarrow$ `IN_PROGRESS` $\rightarrow$ `RESOLVED`).
- **Delete:** Hapus pelanggan (*Soft Delete*) atau hapus tiket komplain yang salah input.

### Modul 6: HR Karyawan, Absensi & Payroll Komisi (`HrPayrollCrudLivewire`)
- **Create:**
  - Tambah karyawan baru (Nama, No. WA, Role `KASIR`/`OPERATOR_CUCI`/`SETRIKA`/`KURIR`, Gaji Pokok, Uang Makan Harian, dan Tarif Komisi per Kg/Trip).
  - Input catatan **Kasbon / Pinjaman Karyawan** baru.
- **Read:**
  - Daftar staf lengkap dengan akumulasi output $\text{Kg}$ bulan berjalan, total komisi terkumpul, dan kalkulasi *Take-Home Pay* otomatis.
  - Preview & cetak **Slip Gaji Digital (PDF/WA)**.
- **Update:**
  - Edit gaji pokok, tarif komisi, jumlah hari hadir absensi, atau potong pelunasan kasbon.
  - **Tutup Buku / Paid Payroll:** Reset akumulasi output $\text{Kg}$ dan kasbon ke $0$ untuk memulai periode penggajian bulan berikutnya (data bulan lalu tersimpan di histori payroll).
- **Delete:** Nonaktifkan atau hapus data karyawan yang sudah *resign*.

### Modul 7: Inventaris Bahan Baku (Sabun/Parfum) & Stok Opname (`InventoryCrudLivewire`)
- **Create:**
  - Tambah item bahan baku baru (SKU, Nama Item, Kategori `CHEMICAL`/`PARFUM`/`PACKAGING`/`GAS`, Satuan `ml`/`Pcs`/`Tabung`, Batas Minimum Alert, Harga Modal per Unit).
  - **Restock / Belanja Gudang (Stok Masuk):** Tambah stok baru saat membeli jerigen deterjen/parfum baru (otomatis menambah `system_stock` dan `actual_stock`).
- **Read:**
  - Visualisasi indikator tangki cairan (*Liquid Tank Progress Bar*), tabel histori pemakaian otomatis, dan laporan **Variance / Audit Selisih Stok**.
- **Update:**
  - **Stok Opname Fisik:** Input jumlah stok fisik aktual di gudang (`actual_stock`). Sistem otomatis menghitung selisih ($\text{Variance} = S_{\text{actual}} - S_{\text{system}}$) untuk mendeteksi pemborosan takaran.
  - Edit detail bahan baku atau harga modal per ml.
- **Delete:** Hapus item inventaris atau batalkan log restock yang salah input.

### Modul 8: Monitoring Utilitas Listrik, Air & Gas (`UtilityCrudLivewire`)
- **Create:** Input pencatatan baru untuk **Listrik PLN ($\text{kWh}$)**, **Air PDAM/Pompa ($m^3$)**, atau **Gas LPG ($\text{Tabung}$)** beserta angka meteran awal, meteran akhir, dan total biaya Rupiah.
- **Read:** Kartu analitik efisiensi biaya Listrik/Kg, Air/Kg, dan Gas/Kg serta tabel riwayat pencatatan utilitas bulanan.
- **Update:** Edit log angka meteran atau koreksi nominal biaya token listrik/tagihan air.
- **Delete:** Hapus catatan log utilitas yang duplikat/salah input (otomatis menghitung ulang angka HPP per Kg di Dashboard).

---

## 4. Kriteria Keberhasilan Aplikasi (Acceptance Criteria)
1. **100% Full CRUD Berfungsi:** Semua entitas (Cabang, Layanan, Order POS, Kanban, Pelanggan CRM, Komplain, Karyawan HR, Inventaris Sabun, dan Log Utilitas) memiliki fitur Tambah, Lihat/Cari, Edit, dan Hapus yang langsung memperbarui state database secara reaktif.
2. **Siap Ekspansi Cabang:** Seluruh tabel utama memiliki `branch_id` yang terhubung ke cabang utama (`id = 1`), lengkap dengan *Branch Switcher Badge* di bagian header.
3. **Pengalaman Mobile Setara Native App:** Saat dibuka di layar smartphone ($< 768\text{px}$), navigasi otomatis beralih ke **Bottom Navigation Bar**, tabel panjang otomatis berubah menjadi **Interactive Mobile Cards**, dan form CRUD muncul sebagai **Slide-Up Bottom Sheet** yang nyaman dioperasikan dengan satu jempol.