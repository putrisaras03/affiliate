<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Daftar command yang bisa dipanggil lewat Artisan
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\FetchShopeeCategories::class,
        \App\Console\Commands\FetchShopeeRecommend::class,
        // kalau ada command lain, tambahkan di sini
    ];

    /**
     * Definisikan schedule untuk command
     */
    protected function schedule(Schedule $schedule): void
    {
        // Contoh: jalankan fetch kategori tiap hari jam 2 pagi
        // $schedule->command('shopee:fetch-categories')->dailyAt('02:00');

        // Contoh: fetch rekomendasi tiap 6 jam
        // $schedule->command('shopee:recommend 11044258 --limit=10 --offset=0')->cron('0 */6 * * *');
    }

    /**
     * Register command untuk aplikasi
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
