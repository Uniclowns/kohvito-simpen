<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        $kasirIds = User::where('id_role', 2)->pluck('id_users')->toArray();
        $mejaIds  = Meja::pluck('id_meja')->toArray();
        $menus    = Menu::select('id_menu', 'harga')->get();

        if ($menus->isEmpty() || empty($mejaIds)) {
            $this->command->warn('Tidak ada data menu atau meja. Jalankan seeder lain terlebih dahulu.');
            return;
        }

        // 70 selesai, 15 diproses, 15 menunggu konfirmasi — diacak
        $statuses = array_merge(
            array_fill(0, 70, ['status_pesanan' => 'selesai',              'status_pembayaran' => 'lunas']),
            array_fill(0, 15, ['status_pesanan' => 'diproses',             'status_pembayaran' => 'menunggu']),
            array_fill(0, 15, ['status_pesanan' => 'menunggu konfirmasi',  'status_pembayaran' => 'menunggu']),
        );
        shuffle($statuses);

        $usedNoPesanan = [];

        foreach ($statuses as $status) {
            $date = Carbon::now()
                ->subDays(rand(0, 29))
                ->setTime(rand(8, 21), rand(0, 59), rand(0, 59));

            do {
                $noPesanan = 'PS-' . $date->format('YmdHis') . '-' . strtoupper(Str::random(4));
            } while (in_array($noPesanan, $usedNoPesanan));

            $usedNoPesanan[] = $noPesanan;

            $selectedMenus = $menus->random(min(rand(1, 4), $menus->count()));
            $totalHarga    = 0;
            $detailItems   = [];

            foreach ($selectedMenus as $menu) {
                $jumlah        = rand(1, 3);
                $subtotal      = $menu->harga * $jumlah;
                $totalHarga   += $subtotal;
                $detailItems[] = [
                    'id_menu'  => $menu->id_menu,
                    'jumlah'   => $jumlah,
                    'catatan'  => null,
                    'subtotal' => $subtotal,
                ];
            }

            Pesanan::create([
                'no_pesanan'        => $noPesanan,
                'id_user'           => $kasirIds ? $kasirIds[array_rand($kasirIds)] : null,
                'id_meja'           => $mejaIds[array_rand($mejaIds)],
                'nama_konsumen'     => $faker->name(),
                'total_harga'       => $totalHarga,
                'status_pembayaran' => $status['status_pembayaran'],
                'status_pesanan'    => $status['status_pesanan'],
                'tgl_pembayaran'    => $status['status_pesanan'] === 'selesai' ? $date : null,
            ]);

            foreach ($detailItems as $item) {
                DetailPesanan::create(array_merge(['no_pesanan' => $noPesanan], $item));
            }
        }

        $this->command->info('100 data pesanan dummy berhasil dibuat.');
    }
}
