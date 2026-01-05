<?php
/**
 * Midtrans Payment Gateway Configuration
 * 
 * Konfigurasi untuk integrasi Midtrans Snap Payment Gateway.
 * Pastikan Anda sudah mendaftar di https://midtrans.com dan mendapatkan
 * Client Key dan Server Key dari dashboard Midtrans.
 */

return [
    // Merchant ID dari Midtrans Dashboard
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),

    // Client Key - digunakan di sisi frontend (JavaScript Snap.js)
    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    // Server Key - digunakan di sisi backend untuk autentikasi API
    'server_key' => env('MIDTRANS_SERVER_KEY'),

    // Set ke true untuk mode production (pembayaran nyata)
    // Set ke false untuk mode sandbox/development
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Mengaktifkan sanitasi pada respons (disarankan true)
    'is_sanitized' => true,

    // Mengaktifkan 3D Secure untuk kartu kredit (disarankan true)
    'is_3ds' => true,

    // Opsional: URL untuk redirect setelah pembayaran
    'finish_url' => env('APP_URL') . '/payment/finish',
    'unfinish_url' => env('APP_URL') . '/payment/unfinish',
    'error_url' => env('APP_URL') . '/payment/error',
];
