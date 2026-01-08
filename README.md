# Sistem Inventaris dan Peminjaman Barang

Proyek ini merupakan aplikasi berbasis web yang dikembangkan menggunakan **Laravel 11** untuk mendigitalisasi proses inventarisasi dan peminjaman barang di lingkungan kampus. Sistem ini membantu pengurus dalam mengelola daftar barang, memantau status peminjaman, serta meningkatkan akuntabilitas anggota melalui sistem denda otomatis yang terintegrasi dengan pembayaran online.

---

## Identitas Kelompok

-   Gabriel Armando Manik (607062430011)
-   Excellence Nachua (607062400032)
-   Nafiza Triana Azzahra (607062400069)
-   Fabio Rifqi Herawan (607062400017)

Kelas: **D3IF – 48 - 04**

---

## Ringkasan Proyek

-   **Tujuan:** Memudahkan pengelolaan inventaris dan peminjaman barang kampus.
-   **Target Pengguna:**
    -   **Admin** → mengelola barang, kategori, dan peminjaman.
    -   **Anggota** → melakukan peminjaman dan pembayaran denda.
-   **Fitur Utama:**
    -   Autentikasi & Otorisasi (Admin/Anggota)
    -   Manajemen Kategori & Barang
    -   Peminjaman & Pengembalian
    -   Denda & Tagihan otomatis
    -   Pembayaran online via Midtrans

---

## Arsitektur & Teknologi

-   **Framework:** Laravel 11
-   **Database:** sqlite
-   **Autentikasi:** Laravel Breeze (Blade + Tailwind CSS)
-   **Payment Gateway:** Midtrans

---

## Breakdown Fitur

| #   | Modul                     | Fitur                 | Deskripsi                                                 | Teknologi                                      | Kriteria Keberhasilan                   |
| --- | ------------------------- | --------------------- | --------------------------------------------------------- | ---------------------------------------------- | --------------------------------------- |
| 1   | Autentikasi & Otorisasi   | Login/Registrasi      | Mengelola akses pengguna berdasarkan role (Admin/Anggota) | Laravel Breeze, Middleware role-based          | User/Admin dapat login sesuai hak akses |
| 2   | Manajemen Kategori        | CRUD Kategori         | Mengelola kategori barang                                 | Controller + Blade form                        | Data kategori tersimpan di DB           |
| 3   | Manajemen Barang          | CRUD Barang           | Menambah/mengedit barang dengan stok & kategori           | Relasi ke tabel `categories`                   | Barang tampil di daftar items           |
| 4   | Peminjaman & Pengembalian | Ajukan & Konfirmasi   | Anggota meminjam, admin menyetujui & mengembalikan        | Relasi ke `users` & `items`, status peminjaman | Status tercatat di DB                   |
| 5   | Denda & Tagihan           | Hitung Denda Otomatis | Sistem menghitung denda keterlambatan                     | Logic di model `Peminjaman`                    | Tagihan muncul di dashboard             |
| 6   | Pembayaran                | Midtrans Integration  | Membayar denda online dengan verifikasi webhook           | API Midtrans + webhook callback                | Status pembayaran valid di DB           |

---

## Rancangan Database

Tabel utama:

-   `users` → data pengguna (Admin/Anggota)
-   `categories` → kategori barang
-   `items` → data barang (relasi ke kategori)
-   `peminjamans` → transaksi peminjaman (relasi ke users & items)
-   `payments` → detail pembayaran denda (relasi ke peminjamans)

---

## Rute Utama

-   `GET /dashboard` → Dashboard sesuai role
-   `GET /items` → Daftar barang tersedia
-   `POST /borrow/{item}` → Ajukan peminjaman
-   `GET /admin/peminjaman` → Kelola peminjaman (Admin)
-   `POST /admin/peminjaman/{id}/approve` → Persetujuan peminjaman
-   `POST /payment/callback` → Callback Midtrans

---

## Sprint / Timeline

1. **Sprint 1 (1 Minggu):** Setup proyek & autentikasi
2. **Sprint 2 (2 Minggu):** CRUD kategori & barang
3. **Sprint 3 (2 Minggu):** Logika peminjaman & pengembalian
4. **Sprint 4 (1 Minggu):** Dashboard Admin & Anggota
5. **Sprint 5 (2 Minggu):** Denda & integrasi Midtrans

---

## Acceptance Criteria

-   **Peminjaman:** hanya bisa dilakukan jika stok > 0, status tercatat sebagai _Diajukan_.
-   **Denda:** otomatis dihitung jika terlambat, status pembayaran tercatat di database.

---

## Testing & Keamanan

-   **Testing:** Feature Test Laravel untuk seluruh alur peminjaman.
-   **Validasi Input:** Laravel Validation.
-   **Otorisasi:** Middleware untuk rute admin.
-   **Proteksi CSRF:** Aktif di semua form POST.

---

## Rencana Pengembangan Lanjut

-   Notifikasi (Email/WhatsApp) pengingat pengembalian.
-   Fitur Booking barang.
-   Dashboard Analytics (barang paling sering dipinjam, anggota paling sering terlambat).

---

## Tabel Ringkasan Fitur

| Modul                     | Nama Fitur            | Fungsi Utama                                       | Langkah Implementasi                       | Acceptance Criteria                     |
| ------------------------- | --------------------- | -------------------------------------------------- | ------------------------------------------ | --------------------------------------- |
| Autentikasi & Otorisasi   | Login/Registrasi      | Mengelola akses pengguna berdasarkan role          | Laravel Breeze, Middleware role-based      | User/Admin dapat login sesuai hak akses |
| Manajemen Kategori        | CRUD Kategori         | Mengelola kategori barang                          | Controller + Blade form                    | Data kategori tersimpan di DB           |
| Manajemen Barang          | CRUD Barang           | Menambah/mengedit barang dengan stok & kategori    | Relasi ke tabel categories                 | Barang tampil di daftar items           |
| Peminjaman & Pengembalian | Ajukan & Konfirmasi   | Anggota meminjam, admin menyetujui & mengembalikan | Relasi ke users & items, status peminjaman | Status tercatat di DB                   |
| Denda & Tagihan           | Hitung Denda Otomatis | Sistem menghitung denda keterlambatan              | Logic di model peminjamans                 | Tagihan muncul di dashboard             |
| Pembayaran                | Midtrans Integration  | Membayar denda online                              | API Midtrans + webhook callback            | Status pembayaran valid di DB           |
