<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

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
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $unreadNotifications = Notification::where('target_user_id', auth()->id())
                    ->where('status', 'unread')
                    ->count();
                $view->with('unreadNotifications', $unreadNotifications);
            }
        });
    }
}
