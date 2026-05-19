<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HistoriPesananDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') && ! env('SEED_HISTORI_DEMO_FORCE')) {
            $this->command->warn('HistoriPesananDemoSeeder di-skip karena environment production. Set SEED_HISTORI_DEMO_FORCE=true untuk override.');

            return;
        }

        $menus = Menu::query()
            ->select('id_menu', 'nama_menu', 'harga')
            ->orderBy('id_menu')
            ->get();

        $mejas = Meja::query()
            ->select('id_meja', 'no_meja')
            ->orderBy('id_meja')
            ->get();

        if ($menus->isEmpty() || $mejas->isEmpty()) {
            $this->command->warn('Seeder histori demo membutuhkan data menu dan meja. Jalankan MenuSeeder dan MejaSeeder terlebih dahulu.');

            return;
        }

        $kasirId = User::query()
            ->where('id_role', 2)
            ->value('id_users') ?? User::query()->value('id_users');

        $today = Carbon::today();
        $orders = $this->demoOrders();
        $created = 0;

        DB::transaction(function () use ($orders, $today, $menus, $mejas, $kasirId, &$created): void {
            $demoOrderIds = Pesanan::query()
                ->where('no_pesanan', 'like', 'HIST-DEMO-%')
                ->pluck('no_pesanan');

            if ($demoOrderIds->isNotEmpty()) {
                DetailPesanan::query()
                    ->whereIn('no_pesanan', $demoOrderIds)
                    ->delete();

                Pesanan::query()
                    ->whereIn('no_pesanan', $demoOrderIds)
                    ->delete();
            }

            foreach ($orders as $index => $order) {
                $noPesanan = sprintf('HIST-DEMO-%03d', $index + 1);
                $detailRows = [];
                $totalHarga = 0;

                foreach ($order['items'] as $itemIndex => $item) {
                    $menu = $this->resolveMenu($menus, $item['menu'] ?? null, $itemIndex);
                    if (! $menu) {
                        continue;
                    }

                    $jumlah = (int) ($item['jumlah'] ?? 1);
                    $subtotal = $menu->harga * $jumlah;
                    $totalHarga += $subtotal;

                    $detailRows[] = [
                        'no_pesanan' => $noPesanan,
                        'id_menu' => $menu->id_menu,
                        'jumlah' => $jumlah,
                        'catatan' => $item['catatan'] ?? null,
                        'subtotal' => $subtotal,
                    ];
                }

                if ($detailRows === []) {
                    continue;
                }

                Pesanan::create([
                    'no_pesanan' => $noPesanan,
                    'id_user' => $kasirId,
                    'id_meja' => $mejas->values()->get($index % $mejas->count())->id_meja,
                    'nama_konsumen' => $order['nama_konsumen'],
                    'total_harga' => $totalHarga,
                    'status_pembayaran' => 'lunas',
                    'status_pesanan' => 'selesai',
                    'catatan_pesanan' => $order['catatan_pesanan'] ?? null,
                    'tgl_pembayaran' => $today->copy()->setTimeFromTimeString($order['jam']),
                ]);

                DetailPesanan::insert($detailRows);
                $created++;
            }
        });

        $this->command->info("OK {$created} histori pesanan demo dibuat untuk hari ini. Buka /kasir/histori untuk melihat UI.");
    }

    private function resolveMenu(Collection $menus, ?string $name, int $offset): ?Menu
    {
        if ($menus->isEmpty()) {
            return null;
        }

        if ($name) {
            $matched = $menus->first(fn (Menu $menu) => strcasecmp($menu->nama_menu, $name) === 0);
            if ($matched) {
                return $matched;
            }
        }

        return $menus->values()->get($offset % $menus->count());
    }

    private function demoOrders(): array
    {
        return [
            [
                'nama_konsumen' => 'Violetta',
                'jam' => '09:15:00',
                'catatan_pesanan' => 'Pesanan dine-in, semua minuman dibuat setelah makanan siap.',
                'items' => [
                    ['menu' => 'Americano', 'jumlah' => 1, 'catatan' => 'Extra ice'],
                    ['menu' => 'Nasi Goreng Spesial', 'jumlah' => 1, 'catatan' => 'Tidak terlalu pedas'],
                    ['menu' => 'Sandwich Club', 'jumlah' => 2, 'catatan' => null],
                    ['menu' => 'Matcha Latte', 'jumlah' => 1, 'catatan' => 'Less sugar'],
                    ['menu' => 'French Fries', 'jumlah' => 1, 'catatan' => 'Saus dipisah'],
                    ['menu' => 'Lemon Squash', 'jumlah' => 2, 'catatan' => null],
                    ['menu' => 'Cappuccino', 'jumlah' => 1, 'catatan' => 'Panas'],
                ],
            ],
            [
                'nama_konsumen' => 'Raka Pratama',
                'jam' => '10:40:00',
                'catatan_pesanan' => null,
                'items' => [
                    ['menu' => 'Caffe Latte', 'jumlah' => 1, 'catatan' => 'Dingin'],
                    ['menu' => 'Roti Bakar', 'jumlah' => 1, 'catatan' => 'Keju cokelat'],
                ],
            ],
            [
                'nama_konsumen' => 'Sari Wulandari',
                'jam' => '12:05:00',
                'catatan_pesanan' => 'Split bill, struk tetap satu.',
                'items' => [
                    ['menu' => 'Mie Goreng', 'jumlah' => 2, 'catatan' => 'Pedas sedang'],
                    ['menu' => 'Es Teh Manis', 'jumlah' => 2, 'catatan' => 'Less sugar'],
                    ['menu' => 'Cheese Stick', 'jumlah' => 1, 'catatan' => null],
                    ['menu' => 'Jus Jeruk', 'jumlah' => 1, 'catatan' => 'Tanpa gula'],
                ],
            ],
            [
                'nama_konsumen' => 'Dion Saputra',
                'jam' => '14:20:00',
                'catatan_pesanan' => 'Take away, pisahkan makanan dan minuman.',
                'items' => [
                    ['menu' => 'Espresso', 'jumlah' => 2, 'catatan' => null],
                    ['menu' => 'Americano', 'jumlah' => 2, 'catatan' => 'Dingin'],
                    ['menu' => 'Cappuccino', 'jumlah' => 1, 'catatan' => 'Panas'],
                    ['menu' => 'Nasi Goreng Spesial', 'jumlah' => 2, 'catatan' => 'Extra sambal'],
                    ['menu' => 'Mie Goreng', 'jumlah' => 1, 'catatan' => 'No onion'],
                    ['menu' => 'French Fries', 'jumlah' => 2, 'catatan' => 'Extra mayo'],
                    ['menu' => 'Lemon Squash', 'jumlah' => 2, 'catatan' => 'Less ice'],
                    ['menu' => 'Matcha Latte', 'jumlah' => 1, 'catatan' => 'Less sugar'],
                    ['menu' => 'Sandwich Club', 'jumlah' => 1, 'catatan' => null],
                ],
            ],
            [
                'nama_konsumen' => 'Maya Putri',
                'jam' => '16:45:00',
                'catatan_pesanan' => null,
                'items' => [
                    ['menu' => 'Teh Tarik', 'jumlah' => 1, 'catatan' => 'Panas'],
                    ['menu' => 'Cokelat Panas', 'jumlah' => 1, 'catatan' => null],
                    ['menu' => 'Cheese Stick', 'jumlah' => 2, 'catatan' => 'Untuk sharing'],
                ],
            ],
            [
                'nama_konsumen' => 'Kevin Anggara',
                'jam' => '19:10:00',
                'catatan_pesanan' => 'Meja minta tambahan tisu dan sendok kecil.',
                'items' => [
                    ['menu' => 'Nasi Goreng Spesial', 'jumlah' => 1, 'catatan' => 'Pedas'],
                    ['menu' => 'Sandwich Club', 'jumlah' => 1, 'catatan' => null],
                    ['menu' => 'Caffe Latte', 'jumlah' => 2, 'catatan' => 'Dingin'],
                    ['menu' => 'Jus Jeruk', 'jumlah' => 2, 'catatan' => 'Tanpa gula'],
                    ['menu' => 'French Fries', 'jumlah' => 1, 'catatan' => null],
                ],
            ],
        ];
    }
}
