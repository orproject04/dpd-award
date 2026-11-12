<?php

namespace App\Http\Filters;

use App\Services\SertifikatService;
use Laravolt\Ui\Filters\BaseFilter;

class SertifikatFilter extends BaseFilter
{
    protected string $label = '';

    public function __construct()
    {
        $this->label = 'Sertifikat';
    }

    public function apply($data, $value)
    {
        if (!$value) {
            return $data;
        }

        $statusFilter = request()->get('status-filter');
        $stageKey = SertifikatService::mapStageKeyFromStatus($statusFilter);
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
        } elseif ($value === 'failed') {
            $data->whereHas('sertifikatEmailLogs', function ($q) use ($scopeStage) {
                $q->where('status', 'failed');
                $scopeStage($q);
            })->whereDoesntHave('sertifikatEmailLogs', function ($q) use ($scopeStage) {
                $q->where('status', 'sent');
                $scopeStage($q);
            });
        } elseif ($value === 'unsent' || $value === 'missing') {
            $idsWithCert = app(SertifikatService::class)->getPendaftarIdsWithCertificate($statusFilter);

            $data->whereDoesntHave('sertifikatEmailLogs', function ($q) use ($scopeStage) {
                $q->where('status', 'sent');
                $scopeStage($q);
            });

            if ($value === 'unsent') {
                if (empty($idsWithCert)) {
                    $data->whereRaw('1 = 0');
                } else {
                    $data->where(function ($q) use ($idsWithCert) {
                        foreach (array_chunk($idsWithCert, 400) as $chunk) {
                            $q->orWhereIn('pendaftar.id', $chunk);
                        }
                    });
                }
            } else {
                foreach (array_chunk($idsWithCert, 400) as $chunk) {
                    $data->whereNotIn('pendaftar.id', $chunk);
                }
            }
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
            'missing' => 'Tidak Ditemukan Sertifikat',
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

