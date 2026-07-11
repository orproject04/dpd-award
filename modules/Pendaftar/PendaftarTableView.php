<?php

namespace Modules\Pendaftar;

use Illuminate\Database\Eloquent\Builder;
use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\RestfulButton;
use Laravolt\Suitable\Columns\Text;
use Laravolt\Ui\TableView;
use Modules\Pendaftar\Models\Pendaftar;

class PendaftarTableView extends TableView
{
    public function data(): Builder
    {
        /** @var Builder */
        $query = Pendaftar::query()
            ->autoSort($this->sortPayload())
            ->autoSearch(trim($this->search));

        return $query->latest();
    }

    public function columns(): array
    {
        return [
            Numbering::make('No'),
            Text::make('foto')->sortable(),
            Text::make('nama')->sortable(),
            Text::make('nomor_registrasi')->sortable(),
            Text::make('kategori')->sortable(),
            RestfulButton::make('modules::pendaftar'),
        ];
    }
}
