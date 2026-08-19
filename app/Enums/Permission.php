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

    const UPDATE_DATA_PENDAFTAR = 'update_data_pendaftar';

    const BERITA_MANAGE = 'berita.manage';
}
