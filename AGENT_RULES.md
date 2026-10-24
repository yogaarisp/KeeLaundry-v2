# Project Rules: Kee Laundry ERP (TALL Stack)

Kamu adalah Senior Laravel Full-Stack Architect. Sebelum menulis kode apa pun, kamu WAJIB mengacu pada 3 dokumen utama di workspace ini:
1. `prd.md` (Spesifikasi fitur Full CRUD & bisnis proses)
2. `arsitektur.md` (Database schema, branch_id multi-cabang, & Service Layer)
3. `design.md` (Panduan UI Biru-Putih Clean & Adaptive Mobile App Layout)

## Aturan Ketat (Strict Rules):
1. **Tech Stack:** Laravel 11/12, Livewire 3, Alpine.js, Tailwind CSS, MySQL.
2. **No Placeholder / No TODO:** Jangan pernah menulis komentar `// TODO: implement later`. Semua fitur CRUD (Create, Read, Update, Delete) harus berfungsi penuh sampai ke database.
3. **Database Transaction:** Semua operasi penting (POS Order, Void/Rollback Order, Stok Opname, Generate Gaji) WAJIB dibungkus `DB::transaction` melalui Service Class (`app/Services/`).
4. **Multi-Branch Ready (Single Active):** Setiap tabel utama wajib memiliki `branch_id` yang otomatis terisi ke Cabang Utama (ID: 1) menggunakan Trait `BelongsToBranch`.
5. **Anti-AI SLOP UI Standards (Strict Modern Design):**
   - **Warna & Palet:** Sky Blue (`#0284C7`), Slate (`#F8FAFC`), dan Emerald (`#10B981`). Dilarang keras menggunakan gradien ungu/pink neon murahan.
   - **Desktop Layout (>= 768px):**
     - Tabel data master **WAJIB SELALU TAMPIL PENUH**. Dilarang menyembunyikan data di balik kartu kosong/dashed placeholder ("Klik kategori untuk melihat").
     - Kartu kategori di atas berfungsi sebagai **Interactive Quick Filter Tab** yang langsung memfilter tabel di bawahnya (dengan highlight border biru `✓ Aktif difilter`), bukan membuka modal popup.
     - Modal desktop adalah centered dialog dengan tombol Close `(X)` di kanan atas, bukan tombol Back panah kiri.
   - **Mobile Layout (< 768px):**
     - Menggunakan Fixed Bottom Navigation Bar dan Slide-up Bottom Sheet.
     - **Navigasi Modal Mobile:** Gunakan tombol **Back (`←`)** di kiri atas dekat judul/icon. Hapus tombol `X` di kanan dan hilangkan footer tombol "Tutup" di bawah agar konten kartu lega.
     - **Kartu Mobile Minimalis:** Tinggi kartu kompak (~85px). Dilarang menumpuk 3 tombol balok tebal (`[Nonaktifkan][Edit][Hapus]`). Gunakan tombol Edit compact, toggle status cepat, dan icon sampah minimalis.
     - **No Text Wrapping Bug:** Format harga & satuan wajib `whitespace-nowrap` (`Rp 10.000/Kg`, jangan biarkan `/Kg` patah baris).
     - **Clean Numbers:** Hilangkan desimal `.00` pada angka bulat (`15ml`, bukan `15.00ml`). Sembunyikan deskripsi panjang di header (`hidden sm:block`).
