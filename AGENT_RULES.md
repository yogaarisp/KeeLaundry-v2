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
5. **UI/UX Mobile App Feel:** 
   - Gunakan palet warna Sky Blue (`#0284C7`) dan Slate (`#F8FAFC`). Dilarang menggunakan gradien ungu/neon (No AI Slop).
   - Desktop menggunakan Sidebar kiri + Tabel.
   - Mobile (`< 768px`) WAJIB menampilkan Bottom Navigation Bar, mengubah tabel menjadi Card List, dan menggunakan Slide-up Bottom Sheet untuk modal form CRUD.