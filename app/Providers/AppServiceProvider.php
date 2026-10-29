<?php

namespace App\Providers;

use App\Models\SchoolProfile;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
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
         */
        Paginator::useBootstrapFive();

        /*
         * Sediakan profil sekolah untuk komponen loading.
         * Logo diambil langsung dari database agar
         * mengikuti perubahan logo pada Profil Sekolah.
         */
        View::composer('components.page-loader', function ($view): void {
            $view->with(
                'loaderSchoolProfile',
                SchoolProfile::query()->first()
            );
        });
    }
}
