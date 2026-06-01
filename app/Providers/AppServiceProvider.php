<?php

namespace App\Providers;

use App\Services\ThermalReceiptPrinter;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Printer struk termal dibangun dari konfigurasi config/printer.php
        // sehingga IP/port/timeout dapat diatur lewat .env tanpa ubah kode.
        $this->app->bind(ThermalReceiptPrinter::class, function () {
            return new ThermalReceiptPrinter(
                (string) config('printer.ip'),
                (int) config('printer.port'),
                (int) config('printer.timeout'),
                (int) config('printer.width'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        App::setLocale('id');
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'id');
    }
}
