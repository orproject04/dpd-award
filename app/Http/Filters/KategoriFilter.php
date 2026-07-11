<?php

namespace App\Http\Filters;

use Laravolt\Ui\Filters\BaseFilter;

class KategoriFilter extends BaseFilter
{
    protected string $label = '';

    public function __construct()
    {
        $this->label = 'Kategori';
    }

    public function apply($data, $value)
    {
        if ($value) {
            $data->where('kategori', $value);
        }

        return $data;
    }

    public function options(): array
    {
        return [
            '' => 'Semua '.$this->label,
            'Bidang Pendidikan' => 'Bidang Pendidikan',
            'Bidang Kesehatan' => 'Bidang Kesehatan',
            'Bidang Ketahanan Pangan' => 'Bidang Ketahanan Pangan',
            'Bidang Seni dan Budaya' => 'Bidang Seni dan Budaya',
        ];
    }

    public function render(): string
    {
        $key = $this->key();

        return form()->dropdown($key)->options($this->options())
            ->label($this->label())
            ->value(request()->get($key))
            ->addClass('clearable');
    }
}
