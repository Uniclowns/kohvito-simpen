<?php

/**
 * Entrypoint serverless Vercel untuk aplikasi Laravel.
 *
 * Di Vercel, filesystem bersifat read-only kecuali direktori /tmp.
 * Laravel butuh lokasi writable untuk meng-compile Blade view saat runtime,
 * jadi kita siapkan struktur direktori di /tmp sebelum mem-boot framework.
 *
 * Driver session & cache memakai database (Supabase), dan log diarahkan ke
 * stderr (lihat Environment Variables di Vercel), sehingga tidak ada lagi
 * penulisan ke storage/ lokal selain compiled view di /tmp.
 */

$writableDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($writableDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Serahkan request ke front controller Laravel.
require __DIR__ . '/../public/index.php';
