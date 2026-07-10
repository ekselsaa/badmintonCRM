<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

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
        Paginator::useBootstrapFive();

        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Bagikan hitungan notifikasi pending ke sidebar admin di setiap request
        // Di-cache 10 menit agar tidak hit DB di setiap page load, di-invalidate saat data berubah
        View::composer('layouts.sidebar-admin', function ($view) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                $view->with([
                    'sidebarPendingBooking'    => Cache::remember(
                        'sidebar_pending_booking',
                        600,
                        fn() => \App\Models\Pembayaran::where('status_verifikasi', 'menunggu')->count()
                    ),
                    'sidebarPendingMembership' => Cache::remember(
                        'sidebar_pending_membership',
                        600,
                        fn() => \App\Models\MembershipPayment::where('status_verifikasi', 'menunggu')->count()
                    ),
                ]);
            }
        });
    }
}
