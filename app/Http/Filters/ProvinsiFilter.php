<?php

namespace App\Http\Filters;

use Laravolt\Ui\Filters\BaseFilter;
use App\Models\Pendaftar;

class ProvinsiFilter extends BaseFilter
{
    protected string $label = '';

    public function __construct()
    {
        $this->label = 'Provinsi';
    }

    public function apply($data, $value)
    {
        if ($value) {
            $data->where('provinsi', $value);
        }

        return $data;
    }

    public function options(): array
    {
        $options = Pendaftar::getProvinsiList();
        $options[''] = 'Semua Provinsi'; // Override "Pilih Provinsi" to "Semua Provinsi"
        return $options;
    }

    public function render(): string
    {
        $key = $this->key();

        return form()->dropdown($key)->options($this->options())
            ->label($this->label())
            ->value(request()->get($key))
            ->addClass('search clearable');
    }
}
