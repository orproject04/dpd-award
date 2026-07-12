<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filters\CustomBaseFilter;
use App\Suitable\CustomBuilder;
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
        $this->app->bind('laravolt.suitable', function ($app) {
            return new CustomBuilder;
        });
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

            // System menu
            $systemMenu = $menu->add('System')
                ->data('order', 99);

            $systemMenu->add('Users', route('epicentrum::users.index'))
                ->active('epicentrum/users/*')
                ->data('icon', 'user-friends')
                ->data('permissions', [\Laravolt\Platform\Enums\Permission::MANAGE_USER]);

            $systemMenu->add('Roles', route('epicentrum::roles.index'))
                ->active('epicentrum/roles/*')
                ->data('icon', 'user-astronaut')
                ->data('permissions', [\Laravolt\Platform\Enums\Permission::MANAGE_ROLE]);

            $systemMenu->add('Permissions', route('epicentrum::permissions.edit'))
                ->active('epicentrum/permissions/*')
                ->data('icon', 'shield-check')
                ->data('permissions', [\Laravolt\Platform\Enums\Permission::MANAGE_PERMISSION]);

            $systemMenu->add('Settings', route('platform::settings.edit'))
                ->active('platform/settings/*')
                ->data('icon', 'sliders-v')
                ->data('permissions', [\Laravolt\Platform\Enums\Permission::MANAGE_SETTINGS]);
        });
    }
}
