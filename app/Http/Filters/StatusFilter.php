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
            'Lolos Pengumuman 50 Besar' => 'Lolos Pengumuman 50 Besar',
            'Lolos Pengumuman 10 Besar' => 'Lolos Pengumuman 10 Besar',
            'Lolos Pengumuman 5 Besar' => 'Lolos Pengumuman 5 Besar',
            'Lolos Tahap Wawancara' => 'Lolos Tahap Wawancara',
            'Lolos Tahap Final' => 'Lolos Tahap Final',
            'Tidak Lolos' => 'Tidak Lolos',
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
