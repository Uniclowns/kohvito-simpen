<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
| Setiap menit, tarik status terbaru dari Midtrans untuk semua pesanan
| yang masih menunggu pembayaran. Failsafe kalau webhook miss atau
| ngrok mati. withoutOverlapping → safe walau run sebelumnya belum
| selesai. runInBackground → tidak blokir tick scheduler berikutnya.
|
| Membutuhkan: `php artisan schedule:work` (dev) atau cron/Task Scheduler
| yang menjalankan `php artisan schedule:run` setiap menit (production).
*/
Schedule::command('payments:sync-pending')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
