<?php

use App\Console\Commands\CloseFlashSales;
use App\Console\Commands\NotifyWishlistPromos;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fase 6: harga promo otomatis aktif/nonaktif & notifikasi wishlist (FR-4.7.9, FR-4.8.2).
Schedule::command(CloseFlashSales::class)->everyMinute()->withoutOverlapping();
Schedule::command(NotifyWishlistPromos::class)->hourly()->withoutOverlapping();
