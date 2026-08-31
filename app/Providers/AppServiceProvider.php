<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filters\CustomBaseFilter;
use App\Suitable\CustomBuilder;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravolt\Ui\Filters\BaseFilter;
use Lavary\Menu\Builder;
use Laravolt\Platform\Enums\Permission;

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
        // Disable Laravolt's global sidebar cache so menu changes apply instantly when roles change
        if (!$this->app->runningInConsole()) {
            try {
                \Illuminate\Support\Facades\Cache::forget('sidebar-items');
            } catch (\Throwable $e) {
                // Ignore errors during deployment or build processes
            }
        }

        if (request()->header('x-forwarded-proto') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }

        if (class_exists(\Laravolt\SemanticForm\SemanticForm::class)) {
            \Laravolt\SemanticForm\SemanticForm::macro('datetime', function ($name, $value = null) {
                $label = null;
                $hint = null;
                $attributes = [];

                if (is_array($name)) {
                    $field = $name;
                    $name = $field['name'];
                    $value = $field['value'] ?? null;
                    $label = $field['label'] ?? null;
                    $hint = $field['hint'] ?? null;
                    $attributes = $field['attributes'] ?? [];
                }

                $element = $this->text($name, $value)
                                ->attribute('type', 'datetime-local')
                                ->attribute('onclick', 'this.showPicker()');

                if ($label) {
                    $element->label($label);
                }
                if ($hint) {
                    $element->hint($hint);
                }
                if (!empty($attributes)) {
                    $element->attributes($attributes);
                }

                return $element;
            });
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

            $user = auth()->user();
            if ($user && ($user->hasPermission('*') || $user->hasPermission(\App\Enums\Permission::BERITA_MANAGE))) {
                $mainMenu->add('Berita', route('berita.index'))
                    ->data('icon', 'newspaper')
                    ->data('order', 2)
                    ->active('berita/*');
            }

            if ($user && ($user->hasPermission('*') || ($user->hasPermission(\App\Enums\Permission::SETTING_ASPEK_MANAGE)))) {
                $mainMenu->add('Setting Aspek', route('kategori-aspek.index'))
                    ->data('icon', 'sliders-h')
                    ->data('order', 3)
                    ->active('kategori-aspek/*');
            }

            // System menu
            $user = auth()->user();

            if (
                $user->hasPermission('*') || $user->hasPermission(Permission::MANAGE_USER)
                || $user->hasPermission(Permission::MANAGE_ROLE)
                || $user->hasPermission(Permission::MANAGE_PERMISSION)
                || $user->hasPermission(Permission::MANAGE_SETTINGS)
            ) {
                $systemMenu = $menu->add('System')
                    ->data('order', 99);

                if ($user->hasPermission('*') || $user->hasPermission(Permission::MANAGE_USER)) {
                    $systemMenu->add('Users', route('epicentrum::users.index'))
                        ->active('epicentrum/users/*')
                        ->data('icon', 'user-friends');
                }

                if ($user->hasPermission('*') || $user->hasPermission(Permission::MANAGE_ROLE)) {
                    $systemMenu->add('Roles', route('epicentrum::roles.index'))
                        ->active('epicentrum/roles/*')
                        ->data('icon', 'user-astronaut');
                }

                if ($user->hasPermission('*') || $user->hasPermission(Permission::MANAGE_PERMISSION)) {
                    $systemMenu->add('Permissions', route('epicentrum::permissions.edit'))
                        ->active('epicentrum/permissions/*')
                        ->data('icon', 'shield-check');
                }

                if ($user->hasPermission('*') || $user->hasPermission(Permission::MANAGE_SETTINGS)) {
                    $systemMenu->add('Settings', route('platform::settings.edit'))
                        ->active('platform/settings/*')
                        ->data('icon', 'sliders-v');
                }
            }
        });
    }
}
