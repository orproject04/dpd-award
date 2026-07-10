<?php

namespace Modules\Pendaftar;

use Laravolt\Support\Base\BaseServiceProvider;
use Livewire\Livewire;

class PendaftarServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();
        Livewire::component('modules.pendaftar.pendaftar-table-view', PendaftarTableView::class);
    }

    public function getIdentifier(): string
    {
        return 'pendaftar';
    }

    public function register(): void
    {
        $file = $this->packagePath("config/{$this->getIdentifier()}.php");
        $this->mergeConfigFrom($file, "modules.{$this->getIdentifier()}");
        $this->publishes([$file => config_path("modules/{$this->getIdentifier()}.php")], 'config');

        $configArray = config("modules.{$this->getIdentifier()}");
        if (is_array($configArray)) {
            $this->config = collect($configArray);
        }
    }

    protected function menu(): void
    {
        app('laravolt.menu.builder')->register(function ($menu) {
            if ($menu->modules) {
                $menu->modules
                    ->add('Pendaftar', route('modules::pendaftar.index'))
                    ->data('icon', 'circle')
                    ->data('permission', $this->config['permission'] ?? [])
                    ->active('modules/pendaftar/*');
            }
        });
    }

    protected function packagePath($path)
    {
        $rc = new \ReflectionClass(get_class($this));
        $dir = dirname($rc->getFileName());

        return sprintf('%s/%s', $dir, $path);
    }
}
