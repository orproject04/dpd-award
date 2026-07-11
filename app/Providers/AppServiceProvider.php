<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filters\CustomBaseFilter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravolt\Ui\Filters\BaseFilter;
use Lavary\Menu\Builder;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     */
    public const string HOME = '/home';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BaseFilter::class, CustomBaseFilter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->header('x-forwarded-proto') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }

        app('laravolt.menu.sidebar')->register(function (Builder $menu) {
            // Main menu
            $mainMenu = $menu->add('Main Menu');
            $mainMenu->add('Dashboard', 'dashboard')
                ->data('icon', 'home')
                ->data('order', 1)
                ->active('dashboard/*');
            $mainMenu->add('Pendaftar', 'pendaftar')
                ->data('icon', 'user')
                ->data('order', 1)
                ->active('pendaftar/*');
        });
    }
}
