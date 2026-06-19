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

/*
 * Arahkan compiled Blade view & cache bootstrap Laravel ke /tmp.
 *
 * `npm run build` tidak menjalankan `view:cache`, jadi Blade dikompilasi
 * saat runtime. Tanpa pengalihan ini Laravel mencoba menulis ke
 * storage/framework/views & bootstrap/cache yang read-only di Vercel,
 * sehingga setiap request menghasilkan HTTP 500.
 *
 * Hanya di-set bila belum didefinisikan, supaya Environment Variables di
 * dashboard Vercel tetap diprioritaskan.
 */
$runtimeEnv = [
    // Log ke stderr (ditangkap Vercel) — driver 'single'/'stack' default
    // menulis ke storage/logs yang read-only di Vercel sehingga error.
    'LOG_CHANNEL'        => 'stderr',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'APP_CONFIG_CACHE'   => '/tmp/bootstrap/cache/config.php',
    'APP_EVENTS_CACHE'   => '/tmp/bootstrap/cache/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
    'APP_ROUTES_CACHE'   => '/tmp/bootstrap/cache/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
];

foreach ($runtimeEnv as $key => $value) {
    if (getenv($key) === false && ! isset($_ENV[$key]) && ! isset($_SERVER[$key])) {
        putenv("{$key}={$value}");
        $_ENV[$key]    = $value;
        $_SERVER[$key] = $value;
    }
}

/*
 * Vercel menerminasi TLS di edge lalu meneruskan request ke function lewat
 * HTTP, padahal URL publik selalu HTTPS. Paksa Laravel mengenali koneksi
 * sebagai secure agar @vite / asset() / url() menghasilkan tautan https://.
 * Tanpa ini, stylesheet ter-link sebagai http:// dan diblokir browser sebagai
 * mixed content, sehingga TailwindCSS tidak termuat.
 */
$_SERVER['HTTPS'] = 'on';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

// Serahkan request ke front controller Laravel.
require __DIR__ . '/../public/index.php';
