# Panduan Konfigurasi Midtrans untuk Demo Pembayaran Denda

## Langkah 1: Edit file .env

Buka file `.env` di root project dan tambahkan/ubah bagian Midtrans:

```env
# ==========================================
# MIDTRANS CONFIGURATION (PEMBAYARAN DENDA)
# ==========================================
# Mode Sandbox (Development) - Ganti ke 'true' untuk Production
MIDTRANS_IS_PRODUCTION=false

# Merchant ID dari Midtrans Dashboard
MIDTRANS_MERCHANT_ID=GANTENG01

# Client Key - Untuk frontend (Snap.js)
MIDTRANS_CLIENT_KEY=SB-Mid-client-your_client_key_here

# Server Key - Untuk backend (API calls)
MIDTRANS_SERVER_KEY=SB-Mid-server-your_server_key_here
```

## Langkah 2: Dapatkan Kredensial dari Midtrans

1. Buka: https://dashboard.midtrans.com
2. Login dengan akun Anda
3. Di menu kiri, klik **Settings** → **Access Keys**
4. Copy:
    - **Client Key** (contoh: `SB-Mid-client-xxxxx`)
    - **Server Key** (contoh: `SB-Mid-server-xxxxx`)

## Langkah 3: Restart Server Laravel

```bash
# Di terminal Laragon
php artisan serve
```

## Langkah 4: Persiapan Data untuk Demo

### A. Buat user test (jika belum ada)

1. Register user baru di aplikasi
2. Atau gunakan script: `php create_test_user.php`

### B. Buat peminjaman dengan denda

1. Login sebagai user
2. Pinjam barang dari halaman Items
3. Login sebagai admin di browser lain
4. Di Admin → Peminjaman:
    - Approve peminjaman
    - Ubah status menjadi "dikembalikan"
    - **PENTING**: Set `tanggal_pengembalian_aktual` ke tanggal yang sudah lewat dari `tanggal_kembali`
    - Denda akan otomatis dihitung (Rp 5.000/hari)

## Langkah 5: Demo Pembayaran Denda

### Alur Demo:

1. **User login** di aplikasi
2. **Buka My Borrowings** → `/my-borrowings`
3. **Cari peminjaman** dengan status "Dikembalikan" dan ada denda
4. **Klik tombol "Bayar Sekarang"** (warna hijau)
5. **Midtrans Snap** akan terbuka dengan detail pembayaran
6. **Pilih metode pembayaran**:
    - Transfer Bank (BCA, BNI, Mandiri, dll)
    - Kartu Kredit (dengan 3DS)
    - E-Wallet (GoPay, OVO, Dana)
7. **Selesaikan pembayaran**
8. **Redirect kembali** ke aplikasi dengan status sukses

### Verifikasi:

Setelah pembayaran:

-   ✅ Kolom `denda_dibayar` = 1 (true)
-   ✅ Kolom `denda_payment_status` = 'paid'
-   ✅ Kolom `denda_paid_at` = tanggal pembayaran
-   ✅ Di halaman My Borrowings, status berubah menjadi "Lunas"

## Troubleshooting

### Jika Snap Token Error:

1. Periksa konfigurasi di `.env`
2. Pastikan `MIDTRANS_IS_PRODUCTION=false` untuk sandbox
3. Restart server: `php artisan serve`

### Jika Callback tidak masuk:

1. Pastikan ngrok/webhook bisa diakses dari internet
2. Untuk lokal, gunakan https://ngrok.com/ untuk tunnel
3. Periksa middleware `VerifyCsrfToken` sudah exclude `/denda/callback`

### Jika Pembayaran selalu "Pending":

1. Midtrans sandbox memerlukan konfirmasi manual untuk beberapa metode
2. Di dashboard Midtrans, klik **Transactions** → **Report**
3. Klik pada transaksi untuk approve

## Catatan Penting

-   **Mode Sandbox**: Semua pembayaran adalah simulasi, tidak ada uang nyata
-   **Test Cards**: Untuk kartu kredit, gunakan test card dari Midtrans:

    -   Success: `4811 1111 1111 1114`
    -   Deny: `4111 1111 1111 1111`
    -   Expired: `4511 1111 1111 1117`
    -   CVV: `123`
    -   Exp: `12/25`

-   **Denda Per Hari**: Rp 5.000 (bisa diubah di `app/Models/Peminjaman.php`)
