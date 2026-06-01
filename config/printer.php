<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Thermal Receipt Printer (Struk Kasir)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi printer struk termal jaringan (ESC/POS) yang dipakai panel
    | Kasir untuk mencetak struk secara langsung. Server membuka socket TCP
    | mentah ke printer pada port RAW/JetDirect (umumnya 9100) dan mengirim
    | byte ESC/POS. Semua nilai dapat ditimpa lewat berkas .env.
    |
    */

    // Alamat IP printer di jaringan (mis. printer wireless 80mm).
    'ip' => env('PRINTER_IP', '192.168.50.230'),

    // Port cetak mentah (RAW / JetDirect). Standar industri = 9100.
    'port' => (int) env('PRINTER_PORT', 9100),

    // Batas waktu (detik) koneksi & tulis socket sebelum dianggap gagal.
    'timeout' => (int) env('PRINTER_TIMEOUT', 5),

    // Lebar kertas dalam karakter (Font A). 80mm ~ 42-48 kolom; 42 = aman.
    'width' => (int) env('PRINTER_WIDTH', 42),

];
