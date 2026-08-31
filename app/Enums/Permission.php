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

    const SETTING_ASPEK_MANAGE = 'setting_aspek.manage';
    

    const PENDIDIKAN_MANAGE = 'pendidikan.manage';
    const KESEHATAN_MANAGE = 'kesehatan.manage';
    const PANGAN_MANAGE = 'pangan.manage';
    const SENI_MANAGE = 'seni.manage';

    public const PENILAIAN_VIEW_TERBATAS = 'penilaian.view_terbatas';

    public static function getCategoryManagePermission(string $kategori): ?string
    {
        return match ($kategori) {
            'Bidang Pendidikan' => self::PENDIDIKAN_MANAGE,
            'Bidang Kesehatan' => self::KESEHATAN_MANAGE,
            'Bidang Ketahanan Pangan' => self::PANGAN_MANAGE,
            'Bidang Seni dan Budaya' => self::SENI_MANAGE,
            default => null,
        };
    }
}
