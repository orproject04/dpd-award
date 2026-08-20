<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class Permission extends Enum
{
    const KTP_VIEW = 'ktp.view';

    const BERITA_MANAGE = 'berita.manage';

    const KONTRIBUSI_PENGHARGAAN_MANAGE = 'kontribusi_penghargaan.manage';
}
