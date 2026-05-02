<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MejaSeeder extends Seeder
{
    /**
     * Seed data contoh untuk tabel meja.
     * Isi: 10 meja dengan QR Code path.
     */
    public function run(): void
    {
        $mejaData = [];

        for ($i = 1; $i <= 10; $i++) {
            $mejaData[] = [
                'no_meja' => 'M' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'qr_code' => 'qrcodes/meja-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '.png',
            ];
        }

        DB::table('meja')->insert($mejaData);
    }
}
