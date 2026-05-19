<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') && ! env('SEED_PESANAN_FORCE')) {
            $this->command->warn('PesananSeeder di-skip karena environment production. Set SEED_PESANAN_FORCE=true untuk override.');

            return;
        }

        $faker = Factory::create('id_ID');

        $kasirIds = User::where('id_role', 2)->pluck('id_users')->toArray();
        $mejaIds = Meja::pluck('id_meja')->toArray();
        $menus = Menu::select('id_menu', 'harga')->get();

        if ($menus->isEmpty() || empty($mejaIds)) {
            $this->command->warn('Tidak ada data menu atau meja. Jalankan seeder lain terlebih dahulu.');

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DetailPesanan::truncate();
        Pesanan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $distributions = [];

        $todayStatuses = $this->statusQueue(['selesai' => 5, 'diproses' => 2, 'menunggu konfirmasi' => 1]);
        for ($i = 0; $i < 8; $i++) {
            $distributions[] = [
                'date' => Carbon::now()->setTime(8 + $i, rand(0, 59), rand(0, 59)),
                'status' => $todayStatuses[$i],
            ];
        }

        $weekStatuses = $this->statusQueue(['selesai' => 18, 'diproses' => 2, 'menunggu konfirmasi' => 2]);
        for ($i = 0; $i < 22; $i++) {
            $daysAgo = rand(1, 6);
            $distributions[] = [
                'date' => Carbon::now()->subDays($daysAgo)->setTime(rand(8, 21), rand(0, 59), rand(0, 59)),
                'status' => $weekStatuses[$i],
            ];
        }

        for ($i = 0; $i < 70; $i++) {
            $daysAgo = rand(7, 29);
            $distributions[] = [
                'date' => Carbon::now()->subDays($daysAgo)->setTime(rand(8, 21), rand(0, 59), rand(0, 59)),
                'status' => ['status_pesanan' => 'selesai', 'status_pembayaran' => 'lunas'],
            ];
        }

        $notes = [
            null, null, null, null,
            'Tidak pedas',
            'Less sugar',
            'Extra cheese',
            'No onion',
            'Tambah es batu',
            'Take away',
        ];

        $catatanPool = [
            'Packaging dipisah untuk masing-masing menu, makanannya di takeaway ya kak, minta plastik dan sendok tambahan.',
            'Tolong makanannya jangan terlalu pedas, ada anak kecil.',
            'Bayar pakai QRIS ya.',
            'Mohon segera diantar, terima kasih.',
            'Tambah es batu di semua minuman.',
        ];

        $usedNoPesanan = [];
        $activeLargeOrders = 0;
        $activeNotes = 0;

        foreach ($distributions as $index => $entry) {
            $date = $entry['date'];
            $status = $entry['status'];
            $isActive = in_array($status['status_pesanan'], ['menunggu konfirmasi', 'diproses'], true);

            do {
                $noPesanan = 'PS-'.$date->format('YmdHis').'-'.strtoupper(Str::random(4));
            } while (in_array($noPesanan, $usedNoPesanan));

            $usedNoPesanan[] = $noPesanan;

            if ($isActive && $activeLargeOrders < 3) {
                $itemCount = rand(5, 7);
                $activeLargeOrders++;
            } else {
                $itemCount = $index % 5 === 0 ? rand(5, 7) : rand(1, 4);
            }

            $catatanPesanan = null;
            if (($isActive && $activeNotes < 2) || $index % 4 === 0) {
                $catatanPesanan = $catatanPool[array_rand($catatanPool)];
                $activeNotes += $isActive ? 1 : 0;
            }

            $selectedMenus = $menus->random(min($itemCount, $menus->count()));
            $totalHarga = 0;
            $detailItems = [];

            foreach ($selectedMenus as $menu) {
                $jumlah = rand(1, 3);
                $subtotal = $menu->harga * $jumlah;
                $totalHarga += $subtotal;
                $detailItems[] = [
                    'id_menu' => $menu->id_menu,
                    'jumlah' => $jumlah,
                    'catatan' => $notes[array_rand($notes)],
                    'subtotal' => $subtotal,
                ];
            }

            Pesanan::create([
                'no_pesanan' => $noPesanan,
                'id_user' => $kasirIds ? $kasirIds[array_rand($kasirIds)] : null,
                'id_meja' => $mejaIds[array_rand($mejaIds)],
                'nama_konsumen' => $faker->name(),
                'total_harga' => $totalHarga,
                'status_pembayaran' => $status['status_pembayaran'],
                'status_pesanan' => $status['status_pesanan'],
                'catatan_pesanan' => $catatanPesanan,
                'tgl_pembayaran' => $date,
            ]);

            foreach ($detailItems as $item) {
                DetailPesanan::create(array_merge(['no_pesanan' => $noPesanan], $item));
            }
        }

        $totalCreated = count($distributions);
        $this->command->info("OK {$totalCreated} pesanan dummy dibuat (8 hari ini, 22 minggu ini, 70 retro).");
    }

    private function statusQueue(array $weights): array
    {
        $bucket = [];
        foreach ($weights as $status => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $bucket[] = $this->statusFor($status);
            }
        }

        shuffle($bucket);

        return $bucket;
    }

    private function statusFor(string $status): array
    {
        return match ($status) {
            'selesai' => [
                'status_pesanan' => 'selesai',
                'status_pembayaran' => 'lunas',
            ],
            'diproses' => [
                'status_pesanan' => 'diproses',
                'status_pembayaran' => 'menunggu',
            ],
            'menunggu konfirmasi' => [
                'status_pesanan' => 'menunggu konfirmasi',
                'status_pembayaran' => 'menunggu',
            ],
        };
    }
}
