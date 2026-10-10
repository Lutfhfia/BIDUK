<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
        /*
         * Gunakan pagination Bootstrap 5.
         * Tampilan BIDUK menggunakan Bootstrap,
         * sehingga pagination Laravel harus mengikuti
         * style Bootstrap dan bukan Tailwind.
         */
        Paginator::useBootstrapFive();
    }
}