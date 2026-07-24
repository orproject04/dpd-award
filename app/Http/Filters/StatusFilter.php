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
            'Tidak Lolos' => 'Tidak Lolos',
            'Diajukan' => 'Diajukan',
            'Lolos Verifikasi Berkas' => 'Lolos Verifikasi Berkas',
            'Lolos ke Tahap 50 Besar' => 'Lolos ke Tahap 50 Besar',
            'Lolos ke Tahap 10 Besar' => 'Lolos ke Tahap 10 Besar',
            'Lolos ke Tahap 5 Besar' => 'Lolos ke Tahap 5 Besar',
            'Lolos ke Tahap Wawancara' => 'Lolos ke Tahap Wawancara',
            'Lolos ke Tahap Final' => 'Lolos ke Tahap Final',
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
