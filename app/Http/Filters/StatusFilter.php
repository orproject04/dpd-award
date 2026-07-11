<?php

namespace App\Http\Filters;

use Laravolt\Ui\Filters\BaseFilter;

class StatusFilter extends BaseFilter
{
    protected string $label = '';

    public function __construct()
    {
        $this->label = 'Status';
    }

    public function apply($data, $value)
    {
        if ($value) {
            $data->where('status', $value);
        }

        return $data;
    }

    public function options(): array
    {
        return [
            '' => 'Semua ' . $this->label,
            'Diajukan' => 'Diajukan',
            'Lolos Verifikasi Berkas' => 'Lolos Verifikasi Berkas',
            'Lolos Penilaian Tahap 1' => 'Lolos Penilaian Tahap 1',
            'Lolos Penilaian Tahap 2' => 'Lolos Penilaian Tahap 2',
            'Lolos Penilaian Tahap 3' => 'Lolos Penilaian Tahap 3',
            'Lolos Tahap Wawancara' => 'Lolos Tahap Wawancara',
            'Lolos Tahap FInal' => 'Lolos Tahap FInal',
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
