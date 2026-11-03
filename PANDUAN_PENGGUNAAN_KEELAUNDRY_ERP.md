# MODUL PANDUAN PENGGUNAAN KEE LAUNDRY ERP (VERSI 2.0)
> **Buku Panduan Operasional Lengkap untuk Pemilik Bisnis, Manajer, Kasir, Operator Produksi, dan Kurir**  
> *Format Terstruktur Khusus untuk Penyerapan Informasi, Analisis, dan Pembuatan Audio Overview di Google NotebookLM*

---

## DAFTAR ISI
1. [Ringkasan Eksekutif & Arsitektur Sistem](#1-ringkasan-eksekutif--arsitektur-sistem)
2. [Modul 1: Autentikasi, Hak Akses & Navigasi Antarmuka](#modul-1-autentikasi-hak-akses--navigasi-antarmuka)
3. [Modul 2: Point of Sale (POS) & Manajemen Transaksi Kasir](#modul-2-point-of-sale-pos--manajemen-transaksi-kasir)
4. [Modul 3: Logistik & Layanan Antar-Jemput (Pickup & Delivery)](#modul-3-logistik--layanan-antar-jemput-pickup--delivery)
5. [Modul 4: Manajemen Operasional Produksi & Kanban Board](#modul-4-manajemen-operasional-produksi--kanban-board)
6. [Modul 5: CRM, Loyalitas Pelanggan & Penanganan Komplain](#modul-5-crm-loyalitas-pelanggan--penanganan-komplain)
7. [Modul 6: Inventaris Bahan Baku, Bill of Materials (BOM) & Utilitas](#modul-6-inventaris-bahan-baku-bill-of-materials-bom--utilitas)
8. [Modul 7: Sumber Daya Manusia (SDM), Absensi & Penggajian (Payroll)](#modul-7-sumber-daya-manusia-sdm-absensi--penggajian-payroll)
9. [Modul 8: Master Layanan, Konfigurasi Toko & Integrasi WhatsApp](#modul-8-master-layanan-konfigurasi-toko--integrasi-whatsapp)
10. [Modul 9: Portal Mandiri Pelacakan Pesanan untuk Pelanggan (Tracking Online)](#modul-9-portal-mandiri-pelacakan-pesanan-untuk-pelanggan-tracking-online)
11. [Modul 10: Dashboard Eksekutif & Analitik Bisnis](#modul-10-dashboard-eksekutif--analitik-bisnis)
12. [Modul 11: Standar Operasional Prosedur (SOP) Harian Laundry](#modul-11-standar-operasional-prosedur-sop-harian-laundry)
13. [Modul 12: Tanya Jawab Sering Diajukan (FAQ) & Panduan Pemecahan Masalah](#modul-12-tanya-jawab-sering-diajukan-faq--panduan-pemecahan-masalah)
14. [Modul 13: Aplikasi Mobile Android (APK) & Pengoperasian Hardware](#modul-13-aplikasi-mobile-android-apk--pengoperasian-hardware)

---

## 1. RINGKASAN EKSEKUTIF & ARSITEKTUR SISTEM

### 1.1 Apa itu Kee Laundry ERP?
**Kee Laundry ERP (v2.0)** adalah sistem informasi enterprise berbasis cloud modern yang dirancang khusus untuk memodernisasi dan mengotomatiskan seluruh siklus operasional bisnis binatu (laundry), mulai dari laundry kiloan, laundry satuan, hingga layanan premium hotel dan instansi. 

Sistem ini menjembatani 4 departemen utama:
1. **Front Office (Kasir):** Melayani pemesanan, timbang pakaian, pembayaran, dan pencetakan nota digital/thermal.
2. **Back Office / Workshop (Produksi):** Melacak status pakaian dari pencucian, pengeringan, setrika uap, hingga pengemasan melalui papan Kanban visual real-time.
3. **Logistik (Kurir Antar-Jemput):** Menjadwalkan jemput dan antar cucian, integrasi rute Google Maps, serta pelaporan timbangan di lapangan.
4. **Manajemen Eksekutif (Owner & Akuntansi):** Menghitung Harga Pokok Produksi (HPP) per kilogram secara akurat, mengontrol stok bahan baku dengan formula otomatis (Bill of Materials), mengukur pemakaian listrik & air, serta menghitung komisi dan gaji bersih karyawan secara otomatis.

### 1.2 Tujuan Utama Implementasi Sistem
- **Menghilangkan Kesalahan Pakaian Tertukar atau Hilang:** Melacak setiap nota pesanan dengan nomor invoice unik, kartu kanban, dan lokasi rak penyimpanan spesifik.
- **Efisiensi Penggunaan Bahan Baku (Zero Waste):** Pengurangan stok sabun deterjen, pelembut (softener), dan parfum secara otomatis berdasarkan bobot cucian yang masuk.
- **Transparansi Layanan kepada Pelanggan:** Pelanggan dapat mengecek langsung proses cucian secara mandiri tanpa perlu menanyakan ke kasir via WhatsApp melalui URL pelacak publik.
- **Penggajian Transparan Berbasis Kinerja:** Sistem menghitung komisi karyawan secara riil berdasarkan kilogram pakaian yang dicuci atau disetrika oleh staf bersangkutan.

---

## MODUL 1: AUTENTIKASI, HAK AKSES & NAVIGASI ANTARMUKA

### 1.1 Hak Akses Pengguna (Role-Based Access Control)
Sistem membedakan fungsi kerja berdasarkan 4 peran utama:
1. **ADMIN (Pemilik / Manajer Cabang):**
   - Memiliki akses ke seluruh menu tanpa batas.
   - Mengatur keuangan, laporan eksekutif, data master layanan, inventaris, utilitas, payroll, dan konfigurasi toko.
2. **KASIR:**
   - Fokus pada menu Kasir POS (`/pos`), riwayat pesanan, top-up deposit pelanggan, pendaftaran komplain, dan penjadwalan antar-jemput.
3. **PRODUKSI:**
   - Fokus pada papan Kanban Produksi (`/produksi`), menggeser status cucian, memilih penanggung jawab pengerjaan, dan menentukan nomor rak penyimpanan pakaian jadi.
4. **KURIR:**
   - Fokus pada manajemen tugas antar-jemput (`/antar-jemput`), melihat alamat jemput/antar, membuka rute Google Maps, dan memperbarui status pengantaran di lapangan.

### 1.2 Fitur Demo & Role Switcher
Untuk memudahkan pelatihan internal dan supervisi manajer, sistem dilengkapi dengan tombol **Role Switcher** di bilah navigasi atas (Desktop) atau menu drawer (Mobile). Manajer dapat berpindah peran dalam hitungan detik tanpa harus logout-login berulang kali:
- Klik `[Switch to KASIR]` -> Langsung diarahkan ke `/pos` dengan antarmuka kasir.
- Klik `[Switch to PRODUKSI]` -> Langsung diarahkan ke `/produksi` dengan antarmuka kanban board.
- Klik `[Switch to KURIR]` -> Langsung diarahkan ke `/antar-jemput` dengan tampilan daftar tugas kurir.
- Klik `[Switch to ADMIN]` -> Kembali ke Dashboard Eksekutif utama.

### 1.3 Akses Khusus Pelanggan (Tanpa Login)
Pelanggan **tidak membutuhkan username atau password** untuk memantau cucian mereka. Mereka dapat langsung membuka tautan publik:
- `https://nama-domain-laundry.com/cek` (atau alias `/lacak` dan `/track`)
- Masukkan nomor invoice yang tertera pada nota (misal: `INV-20261019-0001`).

---

## MODUL 2: POINT OF SALE (POS) & MANAJEMEN TRANSAKSI KASIR

### 2.1 Alur Transaksi Kasir Baru
Penerimaan cucian di front office mengikuti prosedur 6 langkah berikut:

```
[1. Cari / Buat Pelanggan] ➔ [2. Pilih Layanan] ➔ [3. Timbang / Input Qty] ➔ [4. Pilih Parfum & Catatan] ➔ [5. Pembayaran] ➔ [6. Cetak Struk]
```

#### Langkah 1: Memilih atau Menambahkan Pelanggan
1. Buka menu **Kasir POS** (`/pos`).
2. Pada kolom pencarian pelanggan, ketik minimal **2 karakter** nama atau nomor HP pelanggan.
3. Jika pelanggan sudah terdaftar, klik nama pelanggan dari hasil pencarian dropdown. Sistem akan menampilkan rincian:
   - Nama lengkap dan nomor telepon.
   - Status Tier (Regular, Silver, atau VIP).
   - Sisa Saldo Deposit Kiloan (jika ada).
   - Preferensi cucian tersimpan (misal: "Alergi pelembut", "Baju kerja digantung").
4. **Jika Pelanggan Baru (Quick Add):**
   - Klik tombol **"+ Pelanggan Baru"**.
   - Masukkan Nama Lengkap dan Nomor WhatsApp.
   - Klik **"Simpan & Pilih"**. Pelanggan baru langsung aktif dan terpilih untuk pesanan tersebut.

#### Langkah 2: Memilih Paket Layanan
Pilih layanan yang diinginkan oleh pelanggan dari daftar kartu layanan yang tersedia:
- **Kategori Kiloan:**
  - *Cuci Kiloan Reguler* (Estimasi 48 Jam, Satuan: Kg).
  - *Cuci + Setrika Kiloan* (Estimasi 72 Jam, Satuan: Kg).
  - *Cuci Express 8 Jam* (Prioritas cepat untuk cucian mendesak).
- **Kategori Satuan:**
  - *Cuci Bed Cover / Selimut Tebal* (Satuan: Pcs).
  - *Cuci Jas / Blazer Formal* (Satuan: Pcs).
  - *Cuci Karpet* (Satuan: Meter).

#### Langkah 3: Mengisi Kuantitas / Berat Cucian
- Masukkan berat timbangan riil (contoh: `4.5` Kg).
- Sistem secara otomatis menghitung total harga dengan rumus:  
  $$\text{Total Harga} = \text{Harga per Unit} \times \text{Kuantitas}$$

#### Langkah 4: Memilih Aroma Parfum & Instruksi Khusus
- Pilih varian parfum laundry yang diminta pelanggan:
  - **Sakura:** Aroma bunga manis lembut dan tahan lama (favorit pelanggan).
  - **Lavender:** Aroma relaksasi menyegarkan, cocok untuk sprei dan selimut.
  - **Fresh Ocean:** Aroma segar sporty, cocok untuk pakaian harian dan olahraga.
  - **Tanpa Parfum / Unscented:** Untuk pelanggan kulit sensitif atau memiliki alergi wewangian.
- Isi kolom **Catatan Khusus** jika ada permintaan tertentu (contoh: "Ada noda kopi di kerah kemeja putih", "Baju sutra jangan disikat keras").

#### Langkah 5: Memproses Pembayaran
Pilih status dan metode pembayaran:
- **Status Pembayaran:**
  - `LUNAS` (Pelanggan membayar langsung di awal saat menyerahkan pakaian).
  - `BELUM_LUNAS` (Pelanggan memilih bayar nanti saat pakaian selesai diambil).
- **Metode Pembayaran:**
  - `TUNAI` (Uang tunai cash).
  - `QRIS` (Pembayaran barcode dompet digital seperti GoPay, OVO, Dana, BCA, dll).
  - `TRANSFER` (Transfer antar-bank).
  - `DEPOSIT_KG` (Otomatis memotong kuota saldo kiloan prabayar pelanggan).

#### Langkah 6: Simpan Pesanan & Cetak Struk
1. Klik tombol hijau besar **"Proses Order"**.
2. Sistem akan menerbitkan nomor invoice unik (format: `INV-YYYYMMDD-XXXX`).
3. Dialog pratinjau struk (receipt preview) akan muncul secara otomatis.
4. Klik **"Cetak Nota"** untuk mencetak struk fisik pada printer thermal atau klik **"Kirim Struk WA"** untuk mengirim rincian nota langsung ke WhatsApp pelanggan.

---

### 2.2 Pembayaran Menggunakan Saldo Deposit Kiloan
Fitur **Deposit Kiloan** adalah program prabayar di mana pelanggan membeli kuota kilogram di awal (misal paket 25 Kg atau 50 Kg) dengan harga lebih hemat:
1. Ketika pelanggan memilih pembayaran dengan `DEPOSIT_KG`, sistem akan memvalidasi apakah sisa kuota deposit pelanggan mencukupi untuk berat pesanan saat ini.
2. Jika saldo mencukupi (misal sisa 15 Kg, pesanan 4 Kg), status order otomatis menjadi `LUNAS` dengan nominal tagihan Rp 0, dan sistem langsung memotong saldo pelanggan menjadi 11 Kg.
3. Jika kuota tidak mencukupi, sistem akan menolak transaksi dan memberikan notifikasi agar kasir melakukan top-up deposit terlebih dahulu atau menggunakan metode pembayaran tunai/QRIS.

---

### 2.3 Riwayat Transaksi, Edit Pesanan & Pembatalan (Void)
Pada halaman `/pos`, klik tab **"Riwayat Order"**:
- **Pencarian Cepat:** Cari berdasarkan nomor invoice, nama pelanggan, atau nomor telepon.
- **Filter Canggih:** Saring berdasarkan Status Cucian (Antrean, Cuci, Setrika, Packing, Siap Diambil, Selesai), Status Pembayaran (Lunas/Belum), atau Tanggal Masuk.
- **Edit Pesanan:** Dapat dilakukan jika terjadi kesalahan input berat atau pilihan parfum sebelum pesanan diproses di ruang cuci.
- **Void Order (Pembatalan Pesanan):**
  - Hanya dapat dilakukan dengan memasukkan **Alasan Pembatalan Resmi** (misal: "Pelanggan membatalkan order karena buru-buru", "Kesalahan salah input data").
  - Pembatalan pesanan akan mengembalikan saldo stok bahan baku yang sempat terpotong secara otomatis ke dalam sistem.

---

## MODUL 3: LOGISTIK & LAYANAN ANTAR-JEMPUT (PICKUP & DELIVERY)

### 3.1 Konsep Kerja Antar-Jemput
Layanan antar-jemput menghubungkan pelanggan yang berada di rumah atau kantor dengan workshop Kee Laundry melalui kurir toko:
- **Tugas Jemput (Pickup):** Mengambil pakaian kotor dari alamat pelanggan untuk dibawa ke toko laundry.
- **Tugas Antar (Delivery):** Mengirim pakaian yang statusnya sudah `SIAP_DIAMBIL` dari toko ke alamat pelanggan.

### 3.2 Menjadwalkan Tugas Penjemputan Baru
Kasir atau admin dapat mendaftarkan penjemputan dari pelanggan yang memesan via telepon atau WhatsApp:
1. Masuk ke menu **Antar Jemput** (`/antar-jemput`).
2. Klik tombol **"+ Jadwalkan Antar/Jemput"**.
3. Pilih **Jenis Tugas**: `JEMPUT`.
4. Pilih pelanggan dari database (nama, no HP, dan alamat rumah akan otomatis terisi).
5. Masukkan tautan **Google Maps URL** untuk memandu kurir langsung ke koordinat rumah pelanggan via navigasi ponsel.
6. Tentukan **Tanggal Penjemputan** dan **Slot Waktu**:
   - `PAGI` (Pukul 08.00 - 12.00 WIB)
   - `SIANG` (Pukul 12.00 - 16.00 WIB)
   - `SORE` (Pukul 16.00 - 20.00 WIB)
7. Tentukan **Ongkos Kirim (Delivery Fee)** jika berlaku (masukkan `0` jika gratis ongkir).
8. Pilih **Nama Kurir** yang ditugaskan.
9. Masukkan estimasi berat cucian (misal: ~5 Kg) dan catatan rute (misal: "Pagar hitam, rumah seberang masjid").
10. Klik **"Simpan Tugas"**.

### 3.3 Mengirimkan Cucian Bersih (Tugas Antar)
1. Buka menu **Antar Jemput** -> Klik tab **"Siap Antar"**.
2. Pilih nomor pesanan yang berstatus `SIAP_DIAMBIL` yang meminta layanan pengantaran.
3. Sistem secara otomatis menarik alamat pelanggan dan total tagihan yang harus ditagihkan kurir (jika belum lunas).
4. Tugaskan kepada kurir yang sedang beroperasi.

### 3.4 Antarmuka Khusus Kurir di Lapangan
Kurir yang membuka sistem melalui smartphone akan melihat daftar tugas prioritas hari ini:
- **Tombol "Buka Rute Maps":** Membuka aplikasi Google Maps secara otomatis langsung menuju titik lokasi pengantaran.
- **Tombol WhatsApp Pelanggan:** Menghubungi pelanggan dalam satu kali sentuh saat kurir telah sampai di depan rumah pelanggan.
- **Update Status Tugas:**
  - `MENUNGGU` (Tugas telah dijadwalkan).
  - `DIPROSES` (Kurir sedang dalam perjalanan ke lokasi).
  - `SELESAI` (Pakaian berhasil dijemput dan dibawa ke workshop, atau pakaian bersih berhasil diserahkan).
  - `BATAL` (Pelanggan tidak ada di rumah atau penjemputan dibatalkan).

---

## MODUL 4: MANAJEMEN OPERASIONAL PRODUKSI & KANBAN BOARD

### 4.1 Visualisasi Jalur Produksi (Kanban Pipeline)
Operasional workshop diatur melalui antarmuka kartu digital di menu **Produksi** (`/produksi`). Setiap cucian bergerak melalui 5 kolom alur kerja:

```
[1. ANTREAN] ➔ [2. CUCI] ➔ [3. SETRIKA] ➔ [4. PACKING] ➔ [5. SIAP DIAMBIL] ➔ [SELESAI]
```

| Kolom Tahapan | Arti Operasional | Aktivitas Fisik di Ruang Workshop |
| :--- | :--- | :--- |
| **1. ANTREAN** | Pesanan baru masuk | Pakaian kotor telah ditimbang kasir, ditempatkan di keranjang antrean cuci. |
| **2. CUCI** | Proses Pencucian | Operator memasukkan pakaian ke mesin cuci, memberi deterjen & softener sesuai takaran, serta mengoperasikan mesin pengering (dryer). |
| **3. SETRIKA** | Proses Penyetrikaan | Operator menyetrika pakaian dengan setrika uap boiler hingga rapi dan licin. |
| **4. PACKING** | Pemeriksaan & Pengemasan | Pakaian disemprot parfum finishing, dilipat rapi, dimasukkan ke plastik kemasan tebal, dan direkatkan dengan lakban segel higienis. |
| **5. SIAP DIAMBIL** | Siap Diambil / Diantar | Bungkusan pakaian ditempatkan pada rak penyimpanan bernomor dan sistem mengirim notifikasi ke pelanggan. |
| **SELESAI** | Serah Terima Tuntas | Pelanggan telah mengambil pakaian di kasir atau kurir telah selesai mengantarkan ke rumah. |

### 4.2 Prosedur Operator Menggeser Kartu Status
1. Pada kartu pesanan yang ingin dikerjakan, klik tombol **"Update Status"**.
2. Pilih status baru yang dituju (misal dari `ANTREAN` menuju `CUCI`).
3. **Penting - Pilih PIC (Person in Charge):**
   - Pilih nama operator yang mengerjakan (misal: "Budi Santoso").
   - Langkah ini sangat krusial karena sistem mencatat nama operator ini untuk mengalkulasi **komisi produksi per kilogram** pada slip gaji bulanan karyawan!
4. **Penting - Penentuan Lokasi Rak (Saat Masuk Tahap 'Siap Diambil'):**
   - Ketika pesanan digeser ke tahap `SIAP_DIAMBIL`, operator wajib memasukkan kode rak (contoh: `Rak A-03` atau `Gantungan H-02`).
   - Kode rak ini akan muncul di layar kasir dan di halaman pelacak pelanggan, sehingga kasir dapat menemukan bungkusan pakaian pelanggan dalam waktu kurang dari 10 detik saat pelanggan datang ke toko.
5. Klik **"Simpan Perubahan"**.

### 4.3 Tampilan Mode Mobile Ruang Cuci (Mobile Kanban Stage Pill)
Bagi operator yang menggunakan tablet atau smartphone di ruang kerja basah, sistem menyediakan tombol tab horizontal (`ANTREAN`, `CUCI`, `SETRIKA`, `PACKING`, `SIAP DIAMBIL`). Operator tidak perlu menggeser layar lebar ke kanan-kiri, melainkan cukup menekan tab tahapan yang sedang mereka tangani.

---

## MODUL 5: CRM, LOYALITAS PELANGGAN & PENANGANAN KOMPLAIN

### 5.1 Manajemen Basis Data & Segmentasi Tier Pelanggan
Kee Laundry ERP membagi loyalitas pelanggan ke dalam 3 tingkatan (Tier) pada menu **Pelanggan** (`/pelanggan`):
- **REGULAR:** Pelanggan umum yang mencuci sesekali.
- **SILVER:** Pelanggan setia dengan frekuensi cuci rutin.
- **VIP:** Pelanggan prioritas tinggi yang biasanya memiliki saldo deposit kiloan besar.

Setiap profil pelanggan menyimpan catatan preferensi khusus pelanggan, riwayat tanggal order terakhir, total poin reward, dan riwayat pesanan historis.

### 5.2 Fitur Top-Up Saldo Deposit Kiloan
Untuk mengikat retensi pelanggan (cash in advance), kasir dapat menjual paket kuota cuci:
1. Masuk ke menu **Pelanggan**.
2. Cari nama pelanggan, lalu klik tombol **"Top Up Deposit"**.
3. Masukkan jumlah kilogram yang dibeli (contoh: `25` Kg).
4. Masukkan catatan pembayaran (misal: "Paket Promo Gajian 25 Kg Lunas via QRIS").
5. Klik **"Konfirmasi Top Up"**. Saldo deposit pelanggan akan langsung bertambah dan siap dipakai untuk transaksi di POS kapan saja.

### 5.3 Sistem Peringatan Dini Pelanggan Tidak Aktif (Churn Alert)
Pelanggan yang lama tidak datang berisiko pindah ke kompetitor. Tab **"Peringatan Churn"** di menu Pelanggan mendeteksi secara otomatis:
- Pelanggan yang **tidak mencuci lebih dari 14 hari** (Kuning - Perlu Perhatian).
- Pelanggan yang **tidak mencuci lebih dari 30 hari** (Merah - Risiko Churn Tinggi).
- **Aksi Cepat Re-Engagement:** Tersedia tombol langsung **"Kirim Pesan Sapaan / Promo WA"** yang otomatis membuka obrolan WhatsApp personal dengan pesan sapaan hangat dan kode voucher agar pelanggan kembali mencuci.

### 5.4 Penanganan Tiket Komplain (Complaint Ticket Resolution)
Kualitas pelayanan dinilai dari kecepatan menyelesaikan keluhan:
1. Buka tab **"Tiket Komplain"** di menu Pelanggan.
2. Klik tombol **"+ Buat Komplain Baru"**.
3. Pilih pelanggan dan masukkan nomor invoice terkait.
4. Pilih **Kategori Masalah**:
   - `LUNTUR` (Pakaian terkena luntur warna lain).
   - `RUSAK` (Kancing lepas, kain robek, serat tertarik).
   - `HILANG` (Pakaian tertinggal atau kurang jumlah potongannya).
   - `KURANG_BERSIH` (Noda masih tampak nyata).
   - `TERLAMBAT` (Penyelesaian melebihi estimasi jam paket).
5. Tuliskan deskripsi rinci kronologi kejadian dari pelanggan.
6. Pantau status komplain: `OPEN` -> `IN_PROGRESS` -> `RESOLVED`.
7. Catat **Bentuk Kompensasi** yang disepakati (misal: "Penggantian uang tunai Rp 100.000", atau "Gratis cuci kiloan 10 Kg").

---

## MODUL 6: INVENTARIS BAHAN BAKU, BILL OF MATERIALS (BOM) & UTILITAS

### 6.1 Kategori Bahan Baku & Bill of Materials (BOM)
Operasional laundry sangat bergantung pada 4 kategori bahan baku di menu **Inventaris** (`/inventaris`):
1. **CHEMICAL:** Deterjen cair utama, deterjen pencerah warna, pemutih pakaian (bleach).
2. **PARFUM:** Bibit parfum laundry berbagai varian (Sakura, Lavender, Ocean Fresh).
3. **PACKAGING:** Kantong plastik packing berbagai ukuran (35x50, 50x70), hanger kawat, isolasi perekat.
4. **GAS:** Tabung gas LPG 3 Kg atau 12 Kg untuk pemanas mesin dryer konversi dan setrika uap boiler.

#### Mekanisme Otomatis Pemotongan Bahan Baku (Auto-Deduct BOM)
Setiap kali ada pesanan cuci kiloan dibuat di kasir POS, sistem di latar belakang secara otomatis menjalankan formula pengurangan stok sistem:
- **Deterjen Cair:** Berkurang $15\text{ ml}$ per kilogram pakaian.
- **Pelembut (Softener):** Berkurang $10\text{ ml}$ per kilogram pakaian.
- **Parfum Varian Terpilih:** Berkurang $5\text{ ml}$ per kilogram pakaian.

*Keuntungan:* Pemilik bisnis dapat mengetahui sisa stok kimiawi secara riil setiap detik tanpa harus mengukur botol sabun secara manual!

### 6.2 Restock Bahan Baku
Jika barang baru tiba dari pemasok:
1. Klik tombol **"Restock / Tambah Stok"** pada item bahan baku yang bersangkutan.
2. Masukkan jumlah penambahan (contoh: `20000` ml deterjen atau `10` tabung gas).
3. Isi catatan faktur pemasok (misal: "Beli di Toko Bahan Kimia Jaya Abadi").
4. Sistem mencatat mutasi stok masuk (`RESTOCK_IN`) dan menambahkan angka stok sistem.

### 6.3 Prosedur Stock Opname (Penyesuaian Selisih Fisik vs Sistem)
Untuk mencegah kebocoran atau pemborosan takaran oleh operator, audit fisik wajib dilakukan berkala (mingguan atau bulanan):
1. Buka tab **"Stock Opname"** di menu Inventaris.
2. Petugas menimbang sisa bahan baku riil di gudang.
3. Masukkan angka timbangan fisik pada kolom **Stok Aktual**.
4. Sistem akan langsung memperlihatkan nilai selisih (varians):
   - Jika minus: Ada indikasi pemborosan pemakaian atau tumpah.
   - Jika surplus: Ada kemungkinan takaran operator terlalu encer/kurang.
5. Klik **"Simpan & Sesuaikan Stok"**. Sistem menyelaraskan stok sistem dengan stok aktual fisik dan mencatat log mutasi `OPNAME_ADJUST`.

### 6.4 Pencatatan Utilitas Bulanan (Listrik PLN & Air PDAM)
Biaya energi adalah komponen terbesar kedua setelah tenaga kerja. Pada tab **"Catat Utilitas"**:
1. Masukkan data meteran listrik: Angka Meter Awal, Angka Meter Akhir, Total Pemakaian (kWh), dan Biaya Pembelian Token Listrik (Rp).
2. Masukkan data meteran air: Angka Meter Awal, Angka Meter Akhir, Total Kubikasi ($m^3$), dan Biaya Tagihan PDAM (Rp).
3. Sistem menggunakan data ini untuk menghitung secara presisi:  
   $$\text{HPP Riil per Kg Cucian} = \frac{\text{Biaya Bahan Baku} + \text{Biaya Listrik} + \text{Biaya Air} + \text{Biaya Gas}}{\text{Total Kg Pakaian Diproses}}$$

---

## MODUL 7: SUMBER DAYA MANUSIA (SDM), ABSENSI & PENGGAJIAN (PAYROLL)

### 7.1 Struktur Kompensasi Karyawan
Sistem penggajian pada menu **Karyawan** (`/karyawan`) mengadopsi skema insentif modern yang adil dan memotivasi karyawan:
1. **Gaji Pokok Bulanan (Base Salary):** Upah dasar bulanan karyawan.
2. **Uang Makan & Kehadiran Harian (Daily Allowance):** Uang saku per hari dikalikan jumlah hari masuk kerja riil (misal Rp 25.000 / hari $\times 26$ hari hadir = Rp 650.000).
3. **Komisi Output Berbasis Kinerja (Performance Commission):**
   - *Operator Cuci:* Mendapatkan komisi tetap per kg (misal Rp 500 / Kg pakaian yang dicuci).
   - *Operator Setrika:* Mendapatkan komisi tetap per kg (misal Rp 400 / Kg pakaian yang disetrika).
   - *Kurir Logistik:* Mendapatkan komisi tetap per pengantaran (misal Rp 5.000 / trip antar-jemput).
4. **Potongan Kasbon / Pinjaman Karyawan:** Pemotongan cicilan hutang karyawan secara otomatis.

### 7.2 Mengatur Kehadiran & Pencatatan Kasbon
1. Klik tombol **"Presensi & Kasbon"** pada baris karyawan yang bersangkutan.
2. Ubah jumlah hari kehadiran kerja dalam bulan berjalan.
3. Jika karyawan meminjam uang (kasbon), pilih opsi `Tambah Pinjaman` dan masukkan nominal pinjaman.
4. Jika karyawan melunasi pinjaman di luar potong gaji, pilih opsi `Kurangi Pinjaman`.

### 7.3 Penerbitan Slip Gaji Otomatis (Take Home Pay)
Klik tombol **"Lihat Slip Gaji"** untuk melihat ringkasan kalkulasi otomatis:
- Total Penghasilan Bruto = Gaji Pokok + (Hari Hadir $\times$ Uang Harian) + (Total Output Kg $\times$ Tarif Komisi).
- Total Potongan = Sisa Kasbon Karyawan.
- **Gaji Bersih Diterima (Take Home Pay) = Total Penghasilan Bruto - Total Potongan.**
- Slip gaji dapat langsung dicetak rapi dalam format struk thermal atau format cetak kertas untuk diserahkan saat hari gajian.

### 7.4 Tutup Buku Penggajian Bulanan (Close Period)
Di akhir bulan setelah seluruh gaji ditransfer:
1. Manajer mengklik tombol **"Tutup Buku Periode Ini"**.
2. Sistem akan mengunci catatan penggajian periode tersebut dan mereset akumulasi output kilogram produksi serta absensi hari hadir karyawan kembali ke angka 0 untuk memulai bulan kerja yang baru.

---

## MODUL 8: MASTER LAYANAN, KONFIGURASI TOKO & INTEGRASI WHATSAPP

### 8.1 Manajemen Master Layanan & Takaran Kimia
Pada menu **Layanan** (`/layanan`), admin dapat mengatur paket jasa:
- **Nama Layanan:** Misal "Cuci Karpet Masjid", "Cuci Kilat 4 Jam".
- **Kategori:** Kiloan, Satuan, atau Express.
- **Harga Jual per Unit:** Misal Rp 8.000 / Kg.
- **Estimasi Waktu Pengerjaan:** Durasi jam kerja standar (misal 48 jam).
- **Formula Konsumsi Kimia per Unit (BOM Recipe):**
  - Takaran deterjen (ml).
  - Takaran pelembut (ml).
  - Takaran parfum (ml).

### 8.2 Konfigurasi Identitas Cabang & Footer Struk
Pada menu **Pengaturan** (`/pengaturan`):
- Ubah Nama Toko, Alamat Lengkap, Nomor Telepon Resmi, dan Jam Operasional.
- Upload Logo Usaha yang akan tampil di halaman aplikasi dan kop nota.
- **Pesan Footer Struk:** Kalimat syarat & ketentuan yang tercetak di bagian paling bawah struk fisik (misal: *"Barang tidak diambil lebih dari 30 hari di luar tanggung jawab laundry. Komplain maksimal 1x24 jam wajib membawa nota."*).

### 8.3 Pengaturan Printer Thermal Kasir
Sistem mendukung pencetakan nota fisik secara instan tanpa driver rumit:
- **Jenis Printer:** `BLUETOOTH` (Printer portable nirkabel), `USB` (Printer kabel kasir), atau `SYSTEM` (Printer default bawaan laptop/komputer).
- **Ukuran Lebar Kertas:** Pilihan standar `58mm` (printer mini hemat) atau `80mm` (printer kasir standar resto/retail).
- **Auto-Print Nota:** Jika diaktifkan, struk akan otomatis mencetak seketika saat tombol "Proses Order" ditekan oleh kasir.

### 8.4 Personalisasi Template Pesan WhatsApp
Sistem menyediakan automasi pesan WhatsApp dengan tag variabel dinamis:
1. **Template Cucian Siap Diambil:**
   - Dikirimkan saat cucian telah berada di rak penyimpanan.
   - Variabel yang didukung: `{nama}`, `{invoice}`, `{rak}`, `{total}`, `{nama_toko}`, `{link_tracking}`.
   - *Contoh Pesan:*  
     > *"Halo Kak {nama}, cucian Anda dengan nota #{invoice} sudah SELESAI, wangi, dan rapi di {rak}. Total tagihan: {total}. Silakan ambil di {nama_toko}. Cek status detail di: {link_tracking}"*
2. **Template Cucian Selesai / Terima Kasih:**
   - Dikirimkan setelah pakaian diserahkan kepada pelanggan sebagai bukti serah terima dan permintaan ulasan kepuasan bintang 5.
3. **Template Penawaran Promo / Win-Back Churn:**
   - Dikirimkan kepada pelanggan yang sudah lama tidak mencuci untuk memberikan potongan diskon atau voucher gratis antar-jemput.

---

## MODUL 9: PORTAL MANDIRI PELACAKAN PESANAN UNTUK PELANGGAN (TRACKING ONLINE)

### 9.1 Keunggulan Pelacak Mandiri
Halaman `/cek` adalah fitur unggulan Kee Laundry ERP untuk meningkatkan pengalaman pelanggan (Customer Experience). Pelanggan tidak perlu mengunduh aplikasi di Play Store atau App Store, cukup membuka peramban web browser ponsel mereka.

### 9.2 Cara Penggunaan bagi Pelanggan
1. Pelanggan membuka tautan: `https://laundryanda.com/cek` (atau klik tautan yang dikirimkan kasir melalui WhatsApp).
2. Masukkan nomor invoice (misal: `INV-20261019-0001`).
3. Tekan tombol **"Lacak Cucian"**.

### 9.3 Informasi Transparan yang Ditampilkan
Layar ponsel pelanggan akan menampilkan antarmuka modern yang memuat:
- **Stepper Status Visual Real-Time:** Menunjukkan posisi pakaian saat ini: `Antrean` ➔ `Dicuci` ➔ `Disetrika` ➔ `Dipacking` ➔ `Siap Diambil`.
- **Lokasi Rak Penyimpanan:** Jika status sudah siap diambil, pelanggan diberi tahu nomor raknya sehingga proses serah terima di kasir sangat cepat.
- **Rincian Paket & Varian Parfum:** Memastikan varian aroma parfum yang dipilih sudah tepat.
- **Rincian Finansial Transparan:** Berat timbangan riil, harga per kg, total biaya, dan status pelunasan (Lunas / Belum Lunas).
- **Tombol Cepat WhatsApp Toko:** Satu sentuhan untuk langsung membuka pesan WhatsApp jika pelanggan ingin meminta layanan kurir mengantarkan pakaian tersebut ke rumah.

---

## MODUL 10: DASHBOARD EKSEKUTIF & ANALITIK BISNIS

### 10.1 Monitoring Kesehatan Usaha (Executive Dashboard)
Halaman utama sistem (`/`) menyajikan visualisasi data komprehensif bagi pemilik usaha dan manajer:
1. **Metrik Omzet & Finansial:**
   - Total Pendapatan Kotor Hari Ini & Bulan Ini.
   - Nilai Piutang Belum Lunas (uang yang masih tertahan di pakaian yang belum diambil pelanggan).
   - Rata-rata Nilai Transaksi per Pelanggan (Average Order Value).
2. **Metrik Volume Operasional:**
   - Total Kilogram Pakaian Diproses per Hari/Minggu/Bulan.
   - Jumlah Potong Pakaian Satuan yang Diselesaikan.
3. **Analisis HPP vs Target:**
   - Target HPP per Kg yang ditetapkan (misal Rp 1.850 / Kg).
   - Realisasi HPP aktual berdasarkan pemakaian deterjen, listrik, air, dan gas.
   - Peringatan warna merah jika biaya produksi per kg melebihi target toleransi margin keuntungan.
4. **Grafik Komposisi Pembayaran:**
   - Persentase pelanggan yang membayar tunai vs non-tunai (QRIS & Transfer) vs pemakaian Saldo Deposit Kiloan.
5. **Panel Notifikasi Darurat (Action Center):**
   - Peringatan stok bahan kimia yang berada di bawah batas minimum threshold.
   - Jumlah tiket komplain pelanggan yang masih belum terselesaikan (`OPEN`).
   - Jumlah pelanggan berisiko churn tinggi.

---

## MODUL 11: STANDAR OPERASIONAL PROSEDUR (SOP) HARIAN LAUNDRY

### SOP 1: Buka Toko (Opening Checklist - Pukul 07.30 - 08.00)
1. **Kasir:**
   - Nyalakan komputer/tablet kasir dan buka peramban web Kee Laundry ERP.
   - Cek koneksi printer thermal Bluetooth/USB dan pastikan kertas thermal terpasang cukup.
   - Siapkan uang kembalian kas kecil (uang pecahan kecil) di laci kasir.
   - Buka menu **Antar Jemput** untuk melihat jadwal penjemputan slot `PAGI`.
2. **Produksi:**
   - Periksa kran air utama dan pastikan pasokan air PDAM mengalir normal.
   - Periksa regulator gas LPG pada mesin pengering (dryer) dan pastikan tidak ada kebocoran gas.
   - Buka menu **Produksi** (`/produksi`) untuk melihat sisa cucian tahap `ANTREAN` dan `CUCI` dari hari sebelumnya yang harus diprioritaskan pagi ini.

---

### SOP 2: Penerimaan Pakaian di Kasir POS (Pukul 08.00 - 20.00)
1. Sambut pelanggan dengan ramah dan senyuman.
2. Letakkan pakaian pelanggan di atas timbangan digital.
3. Buka menu `/pos`, cari nama pelanggan (atau daftarkan pelanggan baru).
4. Periksa bersama pelanggan apakah ada barang berharga tertinggal di kantong (uang koin, perhiasan, kunci kendaraan, kartu ATM).
5. Catat noda khusus atau bagian pakaian yang robek sebelum dicuci.
6. Konfirmasi pilihan varian parfum (Sakura, Lavender, Fresh Ocean, atau Tanpa Parfum).
7. Konfirmasi metode pembayaran: tawarkan pembelian **Paket Deposit Kiloan** untuk menghemat biaya pelanggan.
8. Cetak nota fisik kasir, sematkan salinan nota kecil pada keranjang cucian pelanggan, dan serahkan nota utama kepada pelanggan atau kirimkan via WhatsApp.

---

### SOP 3: Pelaksanaan Produksi & Pemilahan Pakaian
1. **Sortir Pakaian:**
   - Pisahkan pakaian putih/terang dengan pakaian berwarna pekat untuk mencegah kelunturan.
   - Pisahkan kain berbahan kasar (jeans, jaket) dengan kain halus (sutra, hijab, pakaian dalam).
2. **Pencucian:**
   - Masukkan pakaian ke drum mesin cuci sesuai kapasitas mesin (jangan melebihi batas muatan maksimal drum agar putaran bersih optimal).
   - Gunakan takaran deterjen sesuai standar formula BOM sistem.
   - Operator membuka tablet produksi, klik pesanan terkait, ubah status ke `CUCI`, dan pilih nama operator cuci.
3. **Pengeringan & Penyetrikaan:**
   - Pindahkan pakaian ke dryer hingga kering 100%.
   - Setrika menggunakan setrika uap bertekanan agar pakaian halus sempurna tanpa meninggalkan bekas kilap gosong.
   - Ubah status pesanan ke `SETRIKA` dengan nama operator setrika bersangkutan.
4. **Packing & Penempatan Rak:**
   - Semprotkan parfum laundry finishing secara merata dengan jarak semprot $\pm 20\text{ cm}$.
   - Lipat simetris dan kemas ke dalam kantong plastik kemasan laundry.
   - Pasang label identitas nota di sudut kanan atas plastik kemasan.
   - Pindahkan status pesanan ke `SIAP_DIAMBIL`, ketik kode rak penyimpanan (contoh: `Rak B-02`).
   - Tekan tombol pengiriman notifikasi WhatsApp ke nomor pelanggan.

---

### SOP 4: Serah Terima Pakaian Selesai & Tutup Toko (Closing Checklist - Pukul 20.00 - 20.30)
1. **Serah Terima Pakaian:**
   - Minta pelanggan menyebutkan nama atau nomor nota.
   - Kasir mengecek nomor rak pada sistem dan mengambil pakaian dari rak dalam hitungan detik.
   - Jika pembayaran masih `BELUM_LUNAS`, kasir menerima pembayaran (Tunai/QRIS) dan mengubah status menjadi `LUNAS`.
   - Klik tombol **"Pesanan Selesai / Serahkan"**.
2. **Closing Harian:**
   - Kasir mencocokkan total uang tunai fisik di laci kasir dengan angka laporan kas masuk di sistem.
   - Operator produksi mematikan saklar mesin cuci, menutup kran tabung gas LPG, dan menguras sisa air boiler setrika uap.
   - Manajer melakukan review singkat terhadap target omzet dan pergerakan kanban hari itu.

---

## MODUL 12: TANYA JAWAB SERING DIAJUKAN (FAQ) & PANDUAN PEMECAHAN MASALAH

### Q1: Apa yang harus dilakukan jika printer thermal tidak mau mencetak nota kasir?
- **Pemeriksaan 1:** Periksa koneksi Bluetooth atau kabel USB. Pastikan lampu indikator pada printer menyala hijau (bukan merah kedip/error).
- **Pemeriksaan 2:** Buka menu **Pengaturan** -> Tab **Struk & Printer**. Pastikan jenis printer dipilih dengan benar (`BLUETOOTH` / `USB` / `SYSTEM`) dan ukuran kertas sesuai (`58mm` atau `80mm`).
- **Solusi Alternatif:** Kasir dapat menekan tombol **"Kirim Struk WA"**. Rincian faktur lengkap beserta tautan pelacak online akan langsung terkirim ke WhatsApp pelanggan dalam format teks resmi tanpa memerlukan kertas fisik.

### Q2: Bagaimana jika operator salah memasukkan berat timbangan saat transaksi sudah diproses?
- Selama cucian masih berada pada status `ANTREAN` atau belum selesai dicuci, kasir dapat membuka tab **Riwayat Order**, cari invoice pesanan tersebut, dan klik tombol **"Edit Order"**.
- Perbarui angka kuantitas/berat kg menjadi angka yang benar. Sistem akan otomatis menghitung ulang total harga dan menyesuaikan pengurangan bahan kimia.

### Q3: Pelanggan datang mengambil pakaian tetapi nota fisiknya hilang, bagaimana kasir mencarinya?
- Kasir tidak perlu panik. Buka menu `/pos` atau `/produksi`.
- Pada kolom pencarian, cukup ketik nama panggilan atau 4 digit terakhir nomor WhatsApp pelanggan.
- Sistem akan langsung menampilkan nomor invoice terkait beserta lokasi rak penyimpanannya.

### Q4: Mengapa angka stok deterjen di sistem berbeda dengan jerigen fisik di gudang?
- Perbedaan stok (varians) biasanya terjadi karena:
  1. Operator menuangkan sabun melebihi takaran standar per kilogram (over-dosing).
  2. Terjadi kebocoran kran jerigen atau sabun tumpah.
  3. Ada pesanan offline yang belum sempat terinput di kasir.
- **Solusi:** Lakukan prosedur **Stock Opname** di menu `/inventaris`. Masukkan angka fisik riil, dan sistem akan mencatat selisih tersebut ke dalam log mutasi penyesuaian.

### Q5: Bagaimana jika pelanggan mengajukan komplain pakaian luntur atau hilang?
- Jangan berdebat dengan pelanggan. Ambil nomor invoice pesanan.
- Buka menu **Pelanggan** -> Tab **Tiket Komplain** -> Klik **"+ Buat Komplain Baru"**.
- Catat jenis pakaian dan bukti foto kerusakan.
- Cek riwayat pesanan untuk mengetahui siapa operator cuci (`washed_by`) dan operator setrika (`ironed_by`) yang menangani pesanan tersebut untuk investigasi internal.
- Musyawarahkan bentuk ganti rugi (uang tunai atau kompensasi saldo cuci gratis) dan masukkan nominal kesepakatan pada kolom kompensasi tiket.

---

## MODUL 13: APLIKASI MOBILE ANDROID (APK) & PENGOPERASIAN HARDWARE

### 13.1 Konsep Aplikasi Mobile Kee Laundry
Aplikasi mobile Kee Laundry dibangun dengan teknologi runtime Capacitor yang terhubung langsung ke domain cloud live: `https://laundry.keetech.my.id/`.
- **Keuntungan Utama:** Pembaruan fitur atau harga di server otomatis memperbarui seluruh HP staf tanpa perlu build/install ulang file APK.
- **Penggunaan Layar Penuh (App Experience):** Tidak ada bilah URL browser yang mengganggu, memberikan kenyamanan kerja maksimal bagi kasir dan kurir.

### 13.2 Langkah Pembuatan File APK (.apk)
1. **Menggunakan Android Studio:**
   - Buka folder proyek `android/` di Android Studio.
   - Tunggu proses *Gradle Sync* selesai.
   - Pilih menu **Build** ➔ **Build Bundle(s) / APK(s)** ➔ **Build APK(s)**.
   - Klik **locate** pada notifikasi sukses untuk mendapatkan file `app-debug.apk`.
2. **Menggunakan Terminal / Command Line:**
   - Masuk ke direktori `android/` lalu jalankan perintah: `.\gradlew assembleDebug`.
   - File output APK berada di: `android/app/build/outputs/apk/debug/app-debug.apk`.

### 13.3 Prosedur Instalasi & Izin Perangkat di Smartphone Karyawan
1. Kirim file APK ke smartphone karyawan (via WhatsApp Web, Google Drive, atau kabel USB).
2. Buka pengelola file ➔ ketuk file APK untuk menginstal.
3. Jika muncul notifikasi proteksi, pilih **"Izinkan dari sumber ini"** dan lanjutkan pemasangan.
4. **Pemberian Izin Akses Perangkat:**
   - **Izin Bluetooth:** Wajib diaktifkan untuk HP kasir agar dapat menyambungkan printer thermal nota ukuran 58mm/80mm.
   - **Izin Lokasi (GPS):** Wajib diaktifkan untuk HP kurir agar fitur navigasi Google Maps rute jemput/antar berfungsi optimal.
   - **Izin Kamera:** Diperlukan untuk memotret bukti noda pakaian pada tiket komplain pelanggan.

### 13.4 Menghubungkan Printer Thermal Kasir via Bluetooth
1. Nyalakan printer thermal mini dan aktifkan Bluetooth pada HP kasir.
2. Lakukan *Pairing* (penyandingan) perangkat di menu Bluetooth HP (masukkan PIN: `0000` atau `1234`).
3. Buka aplikasi Kee Laundry ➔ masuk ke menu **Pengaturan** (`/pengaturan`).
4. Pada tab **Struk & Printer**, pilih tipe printer `BLUETOOTH` dan ukuran kertas `58mm` atau `80mm`.
5. Tekan tombol tes cetak untuk memastikan nota fisik berhasil keluar.

---

## RINGKASAN PERINTAH CEPAT (SHORTCUT URL SISTEM)

| Alamat URL | Nama Modul | Fungsi Utama Pengguna |
| :--- | :--- | :--- |
| `/` | **Dashboard Eksekutif** | Pemantauan KPI omzet, HPP per kg, tren order, dan kesehatan bisnis. |
| `/pos` | **Kasir POS & Transaksi** | Pendaftaran pelanggan, timbang pakaian, pembayaran, cetak nota struk. |
| `/produksi` | **Kanban Board Produksi** | Visualisasi alur cuci, setrika, packing, input PIC, penempatan nomor rak. |
| `/antar-jemput` | **Logistik Antar-Jemput** | Penjadwalan kurir, integrasi Google Maps, status pengantaran di jalan. |
| `/pelanggan` | **CRM & Komplain** | Data pelanggan, top-up deposit, deteksi churn, penyelesaian komplain. |
| `/inventaris` | **Inventaris & Utilitas** | Stok sabun/parfum, resep BOM, stock opname, meteran PLN & PDAM. |
| `/karyawan` | **SDM & Payroll** | Data karyawan, absensi, pencatatan kasbon, cetak slip gaji otomatis. |
| `/layanan` | **Master Layanan** | Daftar harga kiloan/satuan, estimasi waktu, takaran deterjen & parfum. |
| `/pengaturan` | **Konfigurasi Cabang** | Profil toko, footer struk, setting printer thermal, template WhatsApp. |
| `/cek` (atau `/lacak`) | **Portal Tracking Mandiri** | Halaman publik bebas login untuk pelanggan melacak status cucian. |

---
*Dokumen ini disusun sebagai panduan standar operasional sistem Kee Laundry ERP v2.0 untuk materi pelatihan digital, standar kerja karyawan, dan integrasi kecerdasan buatan Google NotebookLM.*

