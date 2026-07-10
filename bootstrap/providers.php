<?php

declare(strict_types=1);
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\EventServiceProvider;
use Modules\Pendaftar\PendaftarServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    EventServiceProvider::class,
    PendaftarServiceProvider::class,
];
