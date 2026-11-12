<?php

namespace App\Http\Filters;

use App\Services\SertifikatService;
use Laravolt\Ui\Filters\BaseFilter;

class EmailSertifikatFilter extends BaseFilter
{
    protected string $label = '';

    public function __construct()
    {
        $this->label = 'Email Sertifikat';
    }

    public function apply($data, $value)
    {
        if (!$value) {
            return $data;
        }

        $stageKey = SertifikatService::mapStageKeyFromStatus(request()->get('status-filter'));
        $scopeStage = function ($q) use ($stageKey) {
            if ($stageKey !== null) {
                $q->where('stage_key', $stageKey);
            }
        };

        if ($value === 'sent') {
            $data->whereHas('sertifikatEmailLogs', function ($q) use ($scopeStage) {
                $q->where('status', 'sent');
                $scopeStage($q);
            });
        } elseif ($value === 'unsent') {
            $data->whereDoesntHave('sertifikatEmailLogs', function ($q) use ($scopeStage) {
                $q->where('status', 'sent');
                $scopeStage($q);
            });
        } elseif ($value === 'failed') {
            $data->whereHas('sertifikatEmailLogs', function ($q) use ($scopeStage) {
                $q->where('status', 'failed');
                $scopeStage($q);
            })->whereDoesntHave('sertifikatEmailLogs', function ($q) use ($scopeStage) {
                $q->where('status', 'sent');
                $scopeStage($q);
            });
        }

        return $data;
    }

    public function options(): array
    {
        return [
            '' => 'Semua ' . $this->label,
            'sent' => 'Sudah Dikirim',
            'unsent' => 'Belum Dikirim',
            'failed' => 'Gagal Dikirim',
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
