<?php

namespace Modules\Pendaftar;

use App\Http\Filters\KategoriFilter;
use App\Http\Filters\StatusFilter;
use App\Suitable\CustomTableView;
use Laravolt\Suitable\Columns\Numbering;
use Laravolt\Suitable\Columns\Raw;
use Laravolt\Suitable\Columns\Text;
use Modules\Pendaftar\Models\Pendaftar;
use App\View\Components\MyLabel;
use App\View\Components\MyRestfulButton;

class PendaftarTableView extends CustomTableView
{
    public function source()
    {
        // Simpan URL terakhir beserta filter untuk tombol kembali
        session()->put('pendaftar_index_url', request()->fullUrl());

        $query = Pendaftar::query();

        // Manual filter
        foreach ($this->filters() as $filter) {
            $key = $filter->key();
            $value = request()->get($key);
            $query = $filter->apply($query, $value);
        }

        $hasRestrictedView = auth()->check() && auth()->user()->hasPermission(\App\Enums\Permission::PENILAIAN_VIEW_TERBATAS) && !auth()->user()->hasPermission('*');
        $visibleStatuses = "'Lolos ke Tahap 50 Besar', 'Lolos ke Tahap 10 Besar', 'Lolos ke Tahap 3 Besar', 'Lolos ke Tahap Wawancara', 'Lolos ke Tahap Final'";
        
        $nilaiSelect = $hasRestrictedView 
            ? "CASE WHEN pendaftar.status IN ($visibleStatuses) THEN COALESCE(SUM(total), 0) ELSE -1 END"
            : "COALESCE(SUM(total), 0)";

        return $query
            ->withCount(['kontribusi', 'penghargaan'])
            ->addSelect([
                'nilai' => \App\Models\PendaftarKertasKerja::selectRaw($nilaiSelect)
                    ->whereColumn('pendaftar_kertas_kerja.pendaftar_id', 'pendaftar.id')
                    ->whereColumn('pendaftar_kertas_kerja.tahap', 'pendaftar.status')
            ])
            ->autoSort()
            ->latest('created_at')
            ->autoSearch(request('search'))
            ->paginate(request('per_page') ?? 15);
    }

    public function columns(): array
    {
        $hasRestrictedView = auth()->user()->hasPermission(\App\Enums\Permission::PENILAIAN_VIEW_TERBATAS) && !auth()->user()->hasPermission('*');

        return [
            Numbering::make('No'),
            // Raw::make(
            //     function ($data) {
            //         $fotoRaw = $data->getRawOriginal('foto');
            //         if (!empty($fotoRaw)) {
            //             $src = route('modules::pendaftar.file', ['path' => $fotoRaw]);
            //         } else {
            //             $src = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 24 24' fill='%23cbd5e1'><path d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/></svg>";
            //         }

            //         return "<a href='#' class='avatar-preview' data-src='" . e($src) . "'>" .
            //             "<img class='ui image avatar' src='" . e($src) . "' loading='lazy' style='object-fit:cover;width:32px;height:32px;'>" .
            //             '</a>' .
            //             '<script>' .
            //             'if(!window.avatarPreviewInit){window.avatarPreviewInit=true;' .
            //             "document.addEventListener('click',function(e){" .
            //             "var t=e.target.closest?e.target.closest('.avatar-preview'):null;if(!t)return;e.preventDefault();var s=t.getAttribute('data-src');if(!s)return;" .
            //             "var modal=document.getElementById('avatar-modal');" .
            //             "if(!modal){modal=document.createElement('div');modal.id='avatar-modal';modal.className='ui small modal';modal.innerHTML='<div class=\"content\" style=\"text-align:center;padding:1.5rem;\"><img id=\"avatar-modal-img\" style=\"max-width:100%;max-height:75vh;border-radius:8px;\" src=\"\" /></div>';document.body.appendChild(modal);}" .
            //             "var img=document.getElementById('avatar-modal-img');img.src=s;" .
            //             "if(window.jQuery&&jQuery(modal).modal){jQuery(modal).modal('show');}else{var overlay=document.getElementById('avatar-overlay');if(!overlay){overlay=document.createElement('div');overlay.id='avatar-overlay';overlay.style.cssText='position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);display:flex;align-items:center;justify-content:center;z-index:9999;cursor:pointer;';overlay.addEventListener('click',function(){overlay.style.display='none';});var imgEl=document.createElement('img');imgEl.id='avatar-overlay-img';imgEl.style.cssText='max-width:90%;max-height:90vh;border-radius:8px;';overlay.appendChild(imgEl);document.body.appendChild(overlay);}document.getElementById('avatar-overlay-img').src=s;overlay.style.display='flex';}" .
            //             '},false);}' .
            //             '</script>';
            //     },
            //     ''
            // ),
            Text::make('nama')->sortable(),
            Raw::make(function ($data) {
                $kategoriIcons = [
                    'Pendidikan' => ['icon' => 'graduation cap', 'color' => '#3b82f6'],
                    'Kesehatan' => ['icon' => 'heartbeat', 'color' => '#ec4899'],
                    'Pangan' => ['icon' => 'leaf', 'color' => '#10b981'],
                    'Budaya' => ['icon' => 'theater masks', 'color' => '#f59e0b'],
                ];
                $katKey = collect($kategoriIcons)->keys()->first(fn($k) => str_contains($data->kategori, $k));

                if ($katKey) {
                    $meta = $kategoriIcons[$katKey];
                    return "<div style='text-align:center;'><span style='color:{$meta['color']};font-weight:600;font-size:13px;'><i class='{$meta['icon']} icon' style='margin-right:4px;'></i>{$katKey}</span></div>";
                }

                return "<span style='display:block;text-align:center;'>" . $data->kategori . "</span>";
            }, 'Kategori')->sortable('kategori'),
            Raw::make(function ($data) {
                return "<span style='display:block;text-align:center;'>" . ($data->provinsi ?: '-') . "</span>";
            }, 'Provinsi')->sortable('provinsi'),
            Raw::make(function ($data) {
                return "<span style='display:block;text-align:center;font-family:monospace;font-size:12px;color:var(--text-muted);'>" . $data->nomor_registrasi . "</span>";
            }, 'Nomor Registrasi')->sortable('nomor_registrasi'),
            Raw::make(function ($data) {
                return "<span style='display:block;text-align:center;'>" . $data->kontribusi_count . "</span>";
            }, 'Kontribusi')->sortable('kontribusi_count'),
            Raw::make(function ($data) {
                return "<span style='display:block;text-align:center;'>" . $data->penghargaan_count . "</span>";
            }, 'Penghargaan')->sortable('penghargaan_count'),
            Raw::make(function ($data) {
                return "<span style='display:block;text-align:center;white-space:nowrap;font-size:12px;color:var(--text-muted);'>" . $data->created_at->format('d M Y') . "</span>";
            }, 'Tanggal')->sortable('created_at'),
            MyLabel::make('status')->map([
                'Tidak Lolos' => 'red',
                'Diajukan' => 'blue',
                'Lolos Verifikasi Berkas' => 'yellow',
                'Lolos ke Tahap 50 Besar' => 'yellow',
                'Lolos ke Tahap 10 Besar' => 'yellow',
                'Lolos ke Tahap 3 Besar' => 'yellow',
                'Lolos ke Tahap Wawancara' => 'purple',
                'Lolos ke Tahap Final' => 'teal',
            ])->addClass('large')->sortable(),
            Raw::make(function ($data) use ($hasRestrictedView) {
                $statusRank = \App\Models\Pendaftar::getStatusRank($data->status);
                
                if ($hasRestrictedView && $statusRank < 2) {
                    return "<span style='display:block;text-align:center;font-weight:bold;color:#94a3b8;'>-</span>";
                }

                return "<span style='display:block;text-align:center;font-weight:bold;color:#0284c7;'>" . number_format($data->nilai ?? 0, 2) . "</span>";
            }, 'Nilai')->sortable('nilai'),

            Raw::make(function ($data) use ($hasRestrictedView) {
                $reqPerm = \App\Enums\Permission::getCategoryManagePermission($data->kategori);
                $canManage = auth()->user()->hasPermission('*') || ($reqPerm && auth()->user()->hasPermission($reqPerm));
                
                $actions = collect(['show' => route('modules::pendaftar.show', $data->id)]);
                
                if ($canManage && !$hasRestrictedView) {
                    $actions->put('destroy', route('modules::pendaftar.destroy', $data->id));
                }
                
                $key = \Illuminate\Support\Str::kebab(get_class($data)) . '-' . $data->getKey();
                
                return \Illuminate\Support\Facades\View::make('columns.restful_button', compact('data', 'actions', 'key'))->render();
            }, 'Aksi'),
        ];
    }

    public function filters(): array
    {
        return [
            new KategoriFilter,
            new \App\Http\Filters\ProvinsiFilter,
            new StatusFilter,
        ];
    }
}
