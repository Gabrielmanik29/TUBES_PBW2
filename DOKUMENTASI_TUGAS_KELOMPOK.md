# Dokumentasi Pengembangan Website Laravel - Sistem Inventaris dan Peminjaman Barang

---

## Identitas Mahasiswa dan Kelompok

**Nama Anggota:**

1. Gabriel Armando Manik
2. Excellence Nachua
3. Nafiza Triana Azzahra
4. Fabio Rifqi Herawan

**NIM:**

1. 607062430011
2. 607062400032
3. 607062400069
4. 607062400017

**Kelas:** D3IF – 48 - 04

**Judul Website:** Sistem Inventaris dan Peminjaman Barang

---

## Ringkasan Proyek

Aplikasi web ini bertujuan untuk mendigitalisasi proses inventarisasi dan peminjaman barang di lingkungan Kampus. Tujuan utamanya adalah memudahkan pengurus dalam mengelola daftar barang, memantau status peminjaman, dan meningkatkan akuntabilitas anggota melalui sistem denda keterlambatan otomatis yang terintegrasi dengan pembayaran online.

**Target Pengguna:**

-   **Admin** - Mengelola sistem, kategori, barang, dan peminjaman
-   **Anggota** - Melakukan peminjaman barang dan membayar denda

---

## Arsitektur & Teknologi

| Komponen        | Teknologi      | Alasan Pemilihan                                                                   |
| --------------- | -------------- | ---------------------------------------------------------------------------------- |
| Framework       | Laravel 11     | Versi terbaru yang stabil, ekosistem matang, Eloquent ORM                          |
| Database        | MySQL          | Populer, didukung Laravel, mudah di-deploy                                         |
| Autentikasi     | Laravel Breeze | Implementasi login/registrasi cepat, aman, modern (Blade + Tailwind)               |
| Payment Gateway | Midtrans       | Dokumentasi baik untuk PHP/Laravel, sandbox environment, metode pembayaran lengkap |

---

## Breakdown Fitur

### 1. Modul Autentikasi & Otorisasi

**Fungsi:** Pengguna dapat mendaftar, login, dan logout dengan dua peran (Admin/Anggota)

**Langkah Implementasi:**

1. Install Laravel Breeze
2. Tambah kolom `role` di tabel users
3. Buat method `isAdmin()` dan `isUser()` di User model
4. Buat AdminMiddleware

**Acceptance Criteria:**

-   User bisa login dengan kredensial yang benar
-   Admin dan Anggota punya hak akses berbeda
-   Rute admin dilindungi middleware

---

### 2. Modul Manajemen Kategori

**Fungsi:** Admin dapat melakukan CRUD kategori barang (Elektronik, Properti Acara, dll)

**Langkah Implementasi:**

1. Buat migration `create_categories_table`
2. Buat Category model dengan relationship ke Item
3. Buat CategoryController dengan method index, store, edit, update, destroy
4. Buat view dengan form CRUD

**Acceptance Criteria:**

-   Admin bisa tambah kategori baru
-   Admin bisa edit kategori yang sudah ada
-   Admin bisa hapus kategori (jika tidak ada barang)
-   Kategori tampil di dropdown saat tambah barang

---

### 3. Modul Manajemen Barang

**Fungsi:** Admin dapat CRUD data barang dengan nama, deskripsi, stok, dan kategori

**Langkah Implementasi:**

1. Buat migration `create_items_table` dengan foreign key ke categories
2. Buat Item model dengan accessor `stockTersedia`
3. Buat ItemController dengan CRUD operations
4. Implementasi relasi: Item belongsTo Category, Item hasMany Peminjaman

**Acceptance Criteria:**

-   Barang baru tersimpan di database dengan stok
-   Stok berkurang saat disetujui peminjaman
-   Stok bertambah saat barang dikembalikan
-   Barang tampil di halaman daftar dengan status ketersediaan

---

### 4. Modul Peminjaman & Pengembalian

**Fungsi:** Anggota mengajukan, admin menyetujui/menolak, dan mengonfirmasi pengembalian

**Langkah Implementasi:**

1. Buat migration `create_peminjamans_table` dengan foreign key ke users dan items
2. Buat Peminjaman model dengan relasi
3. Buat PeminjamanController dengan method:
    - `store()` - Ajukan peminjaman
    - `myBorrowings()` - Lihat riwayat user
    - `cancel()` - Batalkan pengajuan
4. Route admin untuk approve/reject

**Status Peminjaman:**

-   `diajukan` - Menunggu persetujuan admin
-   `disetujui` - Approved, barang dipinjam
-   `ditolak` - Ditolak admin
-   `dikembalikan` - Sudah dikembalikan, menunggu konfirmasi admin

**Acceptance Criteria:**

-   Anggota hanya bisa meminjam jika stok tersedia (> 0)
-   Stok otomatis berkurang saat disetujui
-   Status peminjaman tercatat di database
-   Admin bisa ubah status approve/reject

---

### 5. Modul Denda & Tagihan

**Fungsi:** Sistem otomatis menghitung denda keterlambatan

**Langkah Implementasi:**

1. Tambah kolom `denda` dan `denda_dibayar` di tabel peminjamans
2. Buat method `hitungDenda()` di model Peminjaman:
    ```php
    public function hitungDenda()
    {
        if ($this->tanggal_pengembalian_aktual > $this->tanggal_kembali) {
            $terlambat = $this->tanggal_pengembalian_aktual->diffInDays($this->tanggal_kembali);
            return $terlambat * 10000; // Rp 10.000 per hari
        }
        return 0;
    }
    ```
3. Tagihan muncul di dashboard user

**Acceptance Criteria:**

-   Denda otomatis dihitung jika terlambat
-   Tagihan muncul di dashboard anggota
-   Status pembayaran denda tercatat di database

---

### 6. Modul Pembayaran

**Fungsi:** Anggota membayar denda online via Midtrans

**Langkah Implementasi:**

1. Install Midtrans SDK
2. Buat method untuk generate payment URL
3. Buat route `/payment/callback` untuk webhook
4. Update status `denda_dibayar` setelah pembayaran berhasil

**Acceptance Criteria:**

-   Pembayaran tervalidasi melalui callback
-   Status denda_dibayar berubah menjadi true
-   Riwayat pembayaran tersimpan

---

## Rancangan Database

### ERD (Entity Relationship Diagram)

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     users       │       │   categories    │       │     items       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)         │       │ id (PK)         │       │ id (PK)         │
│ name            │       │ name            │       │ name            │
│ email           │       │ created_at      │       │ description     │
│ password        │       │ updated_at      │       │ stock           │
│ role            │       └────────┬────────┘       │ category_id (FK)│
│ created_at      │                │                └────────┬────────┘
│ updated_at      │                │                         │
└────────┬────────┘                │                         │
         │                         │                         │
         │ 1                       │ N                       │ N
         │                         │                         │
┌────────┴────────┐       ┌────────┴─────────┐       ┌──────┴──────┐
│   peminjamans   │       │                  │       │              │
├─────────────────┤       │                  │       │              │
│ id (PK)         │       │                  │       │              │
│ user_id (FK)    │◄──────┘                  └───────►│              │
│ item_id (FK)    │◄──────────────────────────────────┘              │
│ quantity        │                                                  │
│ tanggal_pinjam  │                                                  │
│ tanggal_kembali │                                                  │
│ tanggal_pengembalian_aktual │                                      │
│ status          │       ┌─────────────────┐                       │
│ denda           │       │    payments     │                       │
│ denda_dibayar   │       ├─────────────────┤                       │
│ created_at      │       │ id (PK)         │                       │
│ updated_at      │       │ peminjaman_id   │◄──────────────────────┘
└─────────────────┘       │ midtrans_id     │
                          │ amount          │
                          │ status          │
                          │ created_at      │
                          └─────────────────┘
```

### Deskripsi Tabel

1. **users** - Menyimpan data pengguna (Admin/Anggota)

    - `id`, `name`, `email`, `password`, `role`, `email_verified_at`

2. **categories** - Menyimpan kategori barang

    - `id`, `name`, `created_at`, `updated_at`

3. **items** - Menyimpan data barang

    - `id`, `name`, `description`, `stock`, `category_id`, `created_at`, `updated_at`

4. **peminjamans** - Tabel transaksi peminjaman

    - `id`, `user_id`, `item_id`, `quantity`, `tanggal_pinjam`, `tanggal_kembali`, `tanggal_pengembalian_aktual`, `status`, `denda`, `denda_dibayar`

5. **payments** - Detail pembayaran denda via Midtrans
    - `id`, `peminjaman_id`, `midtrans_id`, `amount`, `status`, `created_at`

---

## Rute Utama

| Method | Route                          | Controller@Method                  | Fungsi               | Middleware     |
| ------ | ------------------------------ | ---------------------------------- | -------------------- | -------------- |
| GET    | /dashboard                     | DashboardController@index          | Redirect sesuai role | auth, verified |
| GET    | /admin/dashboard               | DashboardController@adminDashboard | Dashboard Admin      | auth, admin    |
| GET    | /user/dashboard                | DashboardController@userDashboard  | Dashboard User       | auth, verified |
| GET    | /items                         | ItemController@index               | Daftar barang        | auth           |
| POST   | /borrow/{item}                 | PeminjamanController@store         | Ajukan peminjaman    | auth           |
| GET    | /my-borrowings                 | PeminjamanController@myBorrowings  | Riwayat user         | auth           |
| DELETE | /peminjaman/{id}/cancel        | PeminjamanController@cancel        | Batalkan             | auth           |
| GET    | /admin/peminjaman              | PeminjamanController@adminIndex    | Kelola (Admin)       | auth, admin    |
| PATCH  | /admin/peminjaman/{id}/approve | PeminjamanController@approve       | Setujui              | auth, admin    |
| PATCH  | /admin/peminjaman/{id}/reject  | PeminjamanController@reject        | Tolak                | auth, admin    |
| PATCH  | /admin/peminjaman/{id}/return  | PeminjamanController@confirmReturn | Konfirmasi kembali   | auth, admin    |
| POST   | /payment/callback              | PaymentController@callback         | Callback Midtrans    | -              |

---

## Sprint/Timeline Pengembangan

| Sprint   | Durasi   | Fokus                        | Deliverables                                              |
| -------- | -------- | ---------------------------- | --------------------------------------------------------- |
| Sprint 1 | 1 Minggu | Setup Proyek & Autentikasi   | Laravel project, Breeze installed, User model with role   |
| Sprint 2 | 2 Minggu | CRUD Modul Kategori & Barang | Categories/Items CRUD, Relasi database                    |
| Sprint 3 | 2 Minggu | Peminjaman & Pengembalian    | PeminjamanController, Status management, Stock validation |
| Sprint 4 | 1 Minggu | Dashboard Admin & Anggota    | Admin dashboard, User dashboard, Statistics               |
| Sprint 5 | 2 Minggu | Denda & Midtrans             | hitungDenda(), Midtrans integration, Payment callback     |

---

## Kriteria Penerimaan (Acceptance Criteria)

### Fitur Peminjaman

-   [x] Pengguna hanya dapat meminjam barang jika stok tersedia (> 0)
-   [x] Setelah pengajuan, status tercatat sebagai "Diajukan"
-   [x] Admin dapat melihat dan mengubah status
-   [x] Stok berkurang otomatis saat disetujui
-   [x] Stok bertambah saat barang dikembalikan

### Fitur Denda

-   [x] Denda otomatis dihitung jika tanggal_pengembalian_aktual > tanggal_kembali
-   [x] Tagihan muncul di dashboard user
-   [x] Status pembayaran denda tercatat di database

### Fitur Admin

-   [x] Admin dapat menyetujui/menolak peminjaman
-   [x] Admin dapat mengkonfirmasi pengembalian
-   [x] Dashboard menampilkan statistik lengkap

---

## Testing & Keamanan

### Testing Strategy

**Menggunakan Feature Test Laravel** untuk menyimulasikan alur lengkap:

1. Login anggota
2. Pengajuan pinjam
3. Persetujuan admin
4. Pengembalian terlambat
5. Pembayaran denda

**Contoh Test Case:**

```php
test('anggota dapat mengajukan peminjaman', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['stock' => 10]);

    $this->actingAs($user)
        ->post('/borrow/'.$item->id, [
            'quantity' => 2,
            'tanggal_pinjam' => now(),
            'tanggal_kembali' => now()->addDays(3),
        ])
        ->assertRedirect('/my-borrowings');

    $this->assertDatabaseHas('peminjamans', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'status' => 'diajukan',
    ]);
});
```

### Keamanan

| Aspek             | Implementasi                                     |
| ----------------- | ------------------------------------------------ |
| Validasi Input    | Laravel Validation di semua form                 |
| Otorisasi         | Middleware untuk rute admin (`admin` middleware) |
| Proteksi CSRF     | Otomatis aktif di semua form POST                |
| Auth              | Laravel Breeze dengan hashed password            |
| Role-based Access | Method `isAdmin()` di User model                 |

---

## Kesimpulan

Aplikasi **Sistem Inventaris dan Peminjaman Barang** ini berhasil:

1. ✅ Mengelola inventaris barang kampus secara digital
2. ✅ Memudahkan proses peminjaman dan pengembalian
3. ✅ Meningkatkan disiplin anggota melalui sistem denda otomatis
4. ✅ Menyediakan dashboard terpisah untuk Admin dan Anggota

---

## Rencana Pengembangan Lanjut

| Fitur               | Deskripsi                                           | Prioritas |
| ------------------- | --------------------------------------------------- | --------- |
| Notifikasi          | Mengirim email/WhatsApp pengingat batas waktu       | Tinggi    |
| Booking             | Anggota dapat memesan barang untuk tanggal tertentu | Sedang    |
| Dashboard Analytics | Menampilkan data barang populer, anggota aktif      | Sedang    |
| Laporan Export      | Export PDF/Excel untuk laporan inventaris           | Rendah    |

---

## Tabel Ringkasan Fitur

| Modul                   | Nama Fitur         | Fungsi Utama                      | Langkah Implementasi                                      | Acceptance Criteria                          |
| ----------------------- | ------------------ | --------------------------------- | --------------------------------------------------------- | -------------------------------------------- |
| Autentikasi & Otorisasi | Login/Register     | Sistem autentikasi dengan 2 peran | Install Breeze, Tambah role column, Buat isAdmin() method | User/Admin login sesuai hak akses            |
| Manajemen Kategori      | CRUD Kategori      | Admin kelola kategori barang      | Migration, Controller, Blade form                         | Data kategori tersimpan di DB                |
| Manajemen Barang        | CRUD Items         | Admin kelola barang dengan stok   | Migration items, Relasi category, Controller              | Barang tampil dengan stok tersedia           |
| Peminjaman              | Ajukan Pinjam      | Anggota pinjam barang             | Route POST /borrow/{item}, Validasi stok                  | Stok > 0, Status = "Diajukan"                |
| Peminjaman              | Approve/Tolak      | Admin setujui/tolak               | Route POST /admin/peminjaman/{id}/approve                 | Admin ubah status, Stok berkurang            |
| Pengembalian            | Konfirmasi Kembali | Admin konfirmasi                  | Route PATCH /return, Update stok & status                 | Status = "Dikembalikan", Stok bertambah      |
| Denda                   | Auto Hitung        | Sistem hitung denda keterlambatan | Method hitungDenda() di model                             | Denda = (aktual - kembali) × 10000           |
| Pembayaran              | Midtrans           | Bayar denda online                | API Midtrans, Webhook callback                            | Status denda_dibayar = true                  |
| Dashboard Admin         | Overview Stats     | Admin lihat statistik             | DashboardController@adminDashboard                        | Tampil total items, users, peminjaman, denda |
| Dashboard User          | My Dashboard       | Anggota lihat status              | DashboardController@userDashboard                         | Tampil aktif, tagihan, riwayat               |

---

**Dokumen ini dibuat untuk memenuhi tugas kelompok mata kuliah Pengembangan Basis Web 2**

**Framework:** Laravel 11 | **Database:** MySQL | **Template:** Blade + Tailwind CSS
