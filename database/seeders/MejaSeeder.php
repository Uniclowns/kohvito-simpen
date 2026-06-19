<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MejaSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['id_meja' => 1, 'no_meja' => 'M01', 'qr_code' => 'qrcodes/meja-01.png'],
            ['id_meja' => 2, 'no_meja' => 'M02', 'qr_code' => 'qrcodes/meja-02.png'],
            ['id_meja' => 3, 'no_meja' => 'M03', 'qr_code' => 'qrcodes/meja-03.png'],
            ['id_meja' => 4, 'no_meja' => 'M04', 'qr_code' => 'qrcodes/meja-04.png'],
            ['id_meja' => 5, 'no_meja' => 'M05', 'qr_code' => 'qrcodes/meja-05.png'],
            ['id_meja' => 6, 'no_meja' => 'M06', 'qr_code' => 'qrcodes/meja-06.png'],
            ['id_meja' => 7, 'no_meja' => 'M07', 'qr_code' => 'qrcodes/meja-07.png'],
            ['id_meja' => 8, 'no_meja' => 'M08', 'qr_code' => 'qrcodes/meja-08.png'],
            ['id_meja' => 9, 'no_meja' => 'M09', 'qr_code' => 'qrcodes/meja-09.png'],
            ['id_meja' => 10, 'no_meja' => 'M10', 'qr_code' => 'qrcodes/meja-10.png'],
        ];

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('meja')->upsert($chunk, ['id_meja'], ['no_meja', 'qr_code']);
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('meja', 'id_meja'), COALESCE((SELECT MAX(id_meja) FROM meja), 1), true)");
        }

    }
}
