<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\PageDataComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan View Composer untuk layout app dan welcome
        View::composer(['layouts.app', 'welcome'], PageDataComposer::class);

        // Register global helper for Rupiah formatting
        if (!function_exists('formatRupiah')) {
            function formatRupiah($amount) {
                return 'Rp ' . number_format($amount, 0, ',', '.');
            }
        }
    }
}
