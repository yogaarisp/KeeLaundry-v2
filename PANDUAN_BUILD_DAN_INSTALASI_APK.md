# PANDUAN LENGKAP BUILD & INSTALASI APK ANDROID
# KEE LAUNDRY MOBILE (BERBASIS CAPACITOR)

> **Dokumen Panduan Teknis & Operasional untuk Administrator, Kasir, Kurir, dan Tim Operasional**  
> *Target Domain Live: `https://laundry.keetech.my.id/`*  
> *Format Terstruktur untuk Dokumentasi Teknis dan Analisis Google NotebookLM*

---

## DAFTAR ISI
1. [Ringkasan Arsitektur Aplikasi Mobile](#1-ringkasan-arsitektur-aplikasi-mobile)
2. [Prasyarat Sistem (Prerequisites)](#2-prasyarat-sistem-prerequisites)
3. [Struktur Konfigurasi Capacitor](#3-struktur-konfigurasi-capacitor)
4. [Langkah 1: Build File APK via Android Studio (Rekomendasi)](#langkah-1-build-file-apk-via-android-studio-rekomendasi)
5. [Langkah 2: Build File APK via Terminal / Command Line (Opsi Cepat)](#langkah-2-build-file-apk-via-terminal--command-line-opsi-cepat)
6. [Langkah 3: Panduan Instalasi File APK di Smartphone Android](#langkah-3-panduan-instalasi-file-apk-di-smartphone-android)
7. [Langkah 4: Konfigurasi Izin Perangkat & Hardware (Printer Thermal Bluetooth & GPS)](#langkah-4-konfigurasi-izin-perangkat--hardware-printer-thermal-bluetooth--gps)
8. [Keunggulan Sistem OTA (Over-The-Air Update Otomatis)](#keunggulan-sistem-ota-over-the-air-update-otomatis)
9. [Tanya Jawab & Pemecahan Masalah (Troubleshooting)](#tanya-jawab--pemecahan-masalah-troubleshooting)

---

## 1. RINGKASAN ARSITEKTUR APLIKASI MOBILE

Aplikasi Android **Kee Laundry Mobile** dibangun menggunakan **Capacitor**, teknologi runtime resmi modern dari tim Ionic yang membungkus web aplikasi enterprise Kee Laundry (`https://laundry.keetech.my.id/`) ke dalam shell native Android (.APK).

### Mengapa Pendekatan Ini Sangat Menguntungkan?
1. **Pembaruan Instan (Zero Re-install):** Setiap kali ada pembaruan fitur, perubahan harga, atau perbaikan bug di hosting aaPanel, seluruh HP karyawan dan pelanggan yang sudah memasang APK ini akan **langsung otomatis ter-update** seketika saat aplikasi dibuka tanpa perlu mengunduh file APK baru.
2. **Dukungan Penuh Akses Hardware:**
   - **Bluetooth:** Terkoneksi langsung ke Printer Thermal Kasir ukuran 58mm/80mm untuk cetak struk nota fisik.
   - **Kamera:** Untuk mengunggah foto noda pakaian pada tiket komplain atau foto bukti serah terima cucian.
   - **GPS / Lokasi:** Membimbing kurir navigasi rute jemput/antar pelanggan via Google Maps.
3. **Pengalaman Pengguna Layar Penuh (App Experience):** Tidak ada bilah alamat URL (URL bar) browser, tidak ada tombol navigasi browser yang mengganggu, dan terasa 100% seperti aplikasi native Play Store.

---

## 2. PRASYARAT SISTEM (PREREQUISITES)

Sebelum melakukan proses kompilasi (*build*) APK, pastikan komputer/laptop Anda memiliki:
1. **Node.js (v18 ke atas) & npm:** Sudah terpasang pada sistem.
2. **Android Studio** (Disarankan versi Ladybug / Jellyfish / Flamingo ke atas) ATAU **Java Development Kit (JDK 17/21)**.
3. **Koneksi Internet:** Diperlukan saat pertama kali proses Gradle men-download dependensi Android SDK.

---

## 3. STRUKTUR KONFIGURASI CAPACITOR

Proyek telah dikonfigurasi pada file `capacitor.config.json` di direktori utama:

```json
{
  "appId": "id.keetech.keelaundry",
  "appName": "Kee Laundry",
  "webDir": "public",
  "bundledWebRuntime": false,
  "server": {
    "url": "https://laundry.keetech.my.id",
    "cleartext": true,
    "androidScheme": "https"
  },
  "android": {
    "allowMixedContent": true,
    "captureInput": true,
    "webContentsDebuggingEnabled": true
  }
}
```

### Izin Perangkat (Permissions) yang Disediakan
Pada file `android/app/src/main/AndroidManifest.xml`, sistem telah dibekali izin:
- `android.permission.INTERNET` & `ACCESS_NETWORK_STATE`: Akses ke server cloud.
- `android.permission.CAMERA`: Pengambilan gambar pakaian / komplain.
- `android.permission.ACCESS_FINE_LOCATION`: Navigasi presisi kurir antar-jemput.
- `android.permission.BLUETOOTH_CONNECT` & `BLUETOOTH_SCAN`: Koneksi printer struk thermal portable.

---

## LANGKAH 1: BUILD FILE APK VIA ANDROID STUDIO (REKOMENDASI)

Ini adalah cara paling stabil dan visual untuk menghasilkan file APK siap pakai:

1. **Jalankan Sinkronisasi Terlebih Dahulu (Bila Ada Perubahan Aset):**
   Buka terminal di root project, lalu jalankan:
   ```bash
   npx cap sync
   ```
2. **Buka Proyek Android di Android Studio:**
   - Jalankan aplikasi **Android Studio**.
   - Pilih menu **File** ➔ **Open**.
   - Pilih folder proyek:
     ```text
     g:\laragon\www\keelaundry-v2\android
     ```
   - Klik **OK**.
3. **Tunggu Proses Gradle Sync:**
   - Di sudut kanan bawah Android Studio, perhatikan proses bar *Gradle Sync*.
   - Saat pertama kali, sistem akan mengunduh komponen SDK yang diperlukan (membutuhkan waktu 1–3 menit tergantung kecepatan internet).
   - Pastikan muncul pesan sukses: `BUILD SUCCESSFUL`.
4. **Proses Kompilasi APK (Build APK):**
   - Pada bilah menu atas Android Studio, klik:
     **Build** ➔ **Build Bundle(s) / APK(s)** ➔ **Build APK(s)**.
   - Android Studio akan mulai merender kode menjadi paket instalasi `.apk`.
5. **Mengambil Hasil File APK:**
   - Setelah proses selesai, notifikasi pop-up akan muncul di pojok kanan bawah:  
     > **APK(s) generated successfully for 1 module.**
   - Klik tautan teks berwarna biru: **`locate`**.
   - Jendela Windows Explorer akan terbuka otomatis, menampilkan file bernama:
     ```text
     app-debug.apk
     ```
   - *Tips:* Anda dapat mengganti nama file tersebut menjadi **`KeeLaundry-v2.apk`** agar lebih mudah dikenali saat dibagikan ke staf.

---

## LANGKAH 2: BUILD FILE APK VIA TERMINAL / COMMAND LINE (OPSI CEPAT)

Jika di laptop Anda sudah terkonfigurasi variabel path `JAVA_HOME` dan Android SDK, Anda dapat membuat APK tanpa membuka antarmuka Android Studio:

1. Buka terminal (PowerShell atau Command Prompt) pada direktori proyek.
2. Masuk ke folder Android:
   ```powershell
   cd g:\laragon\www\keelaundry-v2\android
   ```
3. Jalankan perintah kompilasi Gradle:
   ```powershell
   .\gradlew assembleDebug
   ```
4. File APK siap pakai akan otomatis terbentuk di lokasi:
   ```text
   g:\laragon\www\keelaundry-v2\android\app\build\outputs\apk\debug\app-debug.apk
   ```

---

## LANGKAH 3: PANDUAN INSTALASI FILE APK DI SMARTPHONE ANDROID

Setelah file `![alt text](image.png)` (atau `KeeLaundry-v2.apk`) selesai dibuat, ikuti petunjuk berikut untuk memasang di HP:
Setelah file `app-debug.apk` (atau `KeeLaundry-v2.apk`) selesai dibuat, ikuti petunjuk berikut untuk memasang di HP:

### 1. Mengirim File ke Smartphone
Kirimkan file APK ke smartphone karyawan/pengguna melalui salah satu cara berikut:
- **WhatsApp:** Kirim dokumen file APK melalui WhatsApp Web ke nomor kasir/kurir.
- **Kabel Data USB:** Sambungkan HP ke laptop, salin file ke folder *Download*.
- **Google Drive / Mediafire:** Unggah file APK ke Cloud Drive, lalu buka link unduh di peramban HP.

### 2. Membuka & Menginstal APK
1. Buka pengelola file (*File Manager*) di smartphone ➔ masuk ke folder **Download**.
2. Ketuk (tap) pada file **`KeeLaundry-v2.apk`**.
3. **Jika Muncul Peringatan Keamanan ("Sumber Tidak Dikenal / Unknown Sources"):**
   - Tekan tombol **Setelan (Settings)** pada dialog peringatan.
   - Geser toggle aktifkan **"Izinkan dari sumber ini" (Allow from this source)**.
   - Tekan tombol kembali (Back).
4. Tekan tombol **Instal (Install)**.
5. Tunggu proses pemasangan selama beberapa detik hingga muncul keterangan **"Aplikasi terinstal"**.
6. Tekan tombol **Buka (Open)**. Logo dan sistem Kee Laundry akan langsung terbuka secara penuh.

---

## LANGKAH 4: KONFIGURASI IZIN PERANGKAT & HARDWARE

Agar operasional kasir dan kurir berjalan maksimal, lakukan pengaturan izin aplikasi satu kali saat pertama kali dibuka:

### A. Pengaturan Izin Bluetooth (Khusus Kasir Printer Thermal)
1. Nyalakan Printer Thermal Bluetooth Anda (contoh merk: Panda, VSC, Mini POS 58mm/80mm).
2. Di HP kasir, buka **Pengaturan Bluetooth HP** ➔ pasangkan (*Pair*) printer (biasanya PIN: `0000` atau `1234`).
3. Buka aplikasi **Kee Laundry**.
4. Saat aplikasi meminta izin:
   - *"Izinkan Kee Laundry menemukan dan terhubung ke perangkat di sekitar?"* ➔ Pilih **Izinkan (Allow)**.
5. Masuk ke menu **Pengaturan** di dalam aplikasi ➔ Tab **Struk & Printer**:
   - Pilih jenis printer: **BLUETOOTH**.
   - Pilih ukuran kertas: **58mm** atau **80mm**.
   - Klik simpan. Sekarang kasir bisa langsung mencetak nota fisik seketika setelah menekan tombol "Proses Order".

### B. Pengaturan Izin Lokasi & GPS (Khusus Kurir Antar-Jemput)
1. Buka aplikasi Kee Laundry pada HP kurir.
2. Saat aplikasi meminta izin lokasi:
   - *"Izinkan Kee Laundry mengakses lokasi perangkat ini?"* ➔ Pilih **Saat aplikasi digunakan (While using the app)**.
3. Pada menu **Antar Jemput**, kurir cukup menekan tombol **"Buka Rute Maps"** pada kartu penjemputan untuk membuka Google Maps secara presisi.

### C. Pengaturan Izin Kamera (Untuk Kasir & Admin)
- Izinkan akses kamera saat aplikasi meminta izin untuk memotret noda pakaian atau cacat kain pada pendaftaran tiket komplain di menu Pelanggan.

---

## KEUNGGULAN SISTEM OTA (OVER-THE-AIR UPDATE OTOMATIS)

Salah satu kelebihan utama implementasi Capacitor yang mengarah ke domain live (`https://laundry.keetech.my.id/`) adalah:

```
[Developer Update di aaPanel Hosting]
               │
               ▼
   [Live di https://laundry.keetech.my.id]
               │
               ▼
[HP Kasir, Kurir, & Owner Otomatis Terupdate Seketika]
   (TANPA PERLU BUILD ATAU INSTALL APK ULANG!)
```

- Jika Anda mengubah tarif harga layanan di admin, merapikan tampilan struk, atau menambahkan fitur baru di hosting aaPanel, Anda **TIDAK PERLU** membuat APK baru atau menginstal ulang di HP staf.
- Cukup minta kasir/kurir menutup aplikasi lalu membukanya kembali (*refresh*), maka seluruh perubahan akan langsung tampil di layar smartphone.

---

## TANYA JAWAB & PEMECAHAN MASALAH (TROUBLESHOOTING)

### Q1: Layar putih (blank white screen) saat aplikasi baru dibuka di HP, apa penyebabnya?
- **Penyebab:** Smartphone tidak memiliki koneksi internet aktif, atau domain `https://laundry.keetech.my.id/` sedang tidak dapat diakses (SSL expired / server down).
- **Solusi:** Pastikan kuota internet atau koneksi Wi-Fi toko aktif. Cek apakah alamat web dapat dibuka melalui browser Chrome di HP tersebut.

### Q2: Android memblokir instalasi dengan pesan "Play Protect memblokir aplikasi ini", bagaimana mengatasinya?
- **Penyebab:** File APK di-build dalam mode *Debug* / belum ditandatangani dengan sertifikat resmi Google Play Store (sangat wajar untuk aplikasi internal perusahaan).
- **Solusi:** Klik tombol teks kecil **"Tetap Instal (Install Anyway)"** atau **"Rincian Lebih Lanjut"** ➔ pilih **"Tetap Instal"**. Aplikasi 100% aman karena bersumber langsung dari server Anda sendiri.

### Q3: Printer Bluetooth tidak mencetak struk dari aplikasi?
1. Pastikan Bluetooth di smartphone menyala.
2. Pastikan printer tidak sedang terhubung (*connected*) ke perangkat HP kasir lain (printer thermal hanya bisa menerima 1 koneksi Bluetooth dalam satu waktu).
3. Matikan saklar printer selama 5 detik, lalu nyalakan kembali.
4. Coba lakukan cetak tes melalui menu **Pengaturan** ➔ **Cetak Tes Struk**.

### Q4: Apakah pelanggan umum bisa memakai file APK ini?
- **Bisa.** Ketika pelanggan membuka aplikasi, mereka cukup mengakses menu pelacak publik di `/cek` untuk memantau status cucian mereka secara mandiri tanpa memerlukan akun login.

---
*Dokumen ini disusun untuk melengkapi operasional sistem Kee Laundry ERP v2.0.*

