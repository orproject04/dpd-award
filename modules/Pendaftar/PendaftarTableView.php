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
        $query = Pendaftar::query();

        // Manual filter
        foreach ($this->filters() as $filter) {
            $key = $filter->key();
            $value = request()->get($key);
            $query = $filter->apply($query, $value);
        }

        return $query
            ->withCount(['kontribusi', 'penghargaan'])
            ->autoSort()
            ->latest('updated_at')
            ->autoSearch(request('search'))
            ->paginate(request('per_page') ?? 15);
    }

    public function columns(): array
    {
        return [
            Numbering::make('No'),
            Raw::make(
                function ($data) {
                    $path = $data->getFotoAttribute();
                    $src = '';
                    $defaultPath = resource_path('images/avatar.png');
                    if (file_exists($defaultPath) && is_file($defaultPath)) {
                        $type = pathinfo($defaultPath, PATHINFO_EXTENSION);
                        $fileData = file_get_contents($defaultPath);
                        $src = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
                    }
                    if (!empty($path) && file_exists($path) && is_file($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $fileData = file_get_contents($path);
                        $src = 'data:image/' . $type . ';base64,' . base64_encode($fileData);
                    }

                    return "<a href='#' class='avatar-preview' data-src='" . $src . "'>" .
                        "<img class='ui image avatar' src='" . $src . "'>" .
                        '</a>' .
                        '<script>' .
                        '(function(){' .
                        'if(window.avatarPreviewInit) return; window.avatarPreviewInit=true;' .
                        "document.addEventListener('click',function(e){" .
                        "var t=e.target.closest?e.target.closest('.avatar-preview'):null; if(!t) return; e.preventDefault(); var s=t.getAttribute('data-src');" .
                        "var modal=document.getElementById('avatar-modal');" .
                        "if(!modal){ modal=document.createElement('div'); modal.id='avatar-modal'; modal.className='ui small modal'; modal.innerHTML='<div class=\"content\" style=\"text-align:center\"><img id=\"avatar-modal-img\" style=\"max-width:100%;height:auto\" src=\"\" /></div>'; document.body.appendChild(modal); }" .
                        "var img=document.getElementById('avatar-modal-img'); img.src=s;" .
                        "if(window.jQuery && jQuery(modal).modal){ jQuery(modal).modal('show'); } else { var overlay=document.getElementById('avatar-overlay'); if(!overlay){ overlay=document.createElement('div'); overlay.id='avatar-overlay'; overlay.style.position='fixed'; overlay.style.top=0; overlay.style.left=0; overlay.style.width='100%'; overlay.style.height='100%'; overlay.style.background='rgba(0,0,0,0.6)'; overlay.style.display='flex'; overlay.style.alignItems='center'; overlay.style.justifyContent='center'; overlay.style.zIndex=9999; overlay.addEventListener('click',function(){ overlay.style.display='none'; }); var imgEl=document.createElement('img'); imgEl.id='avatar-overlay-img'; imgEl.style.maxWidth='90%'; imgEl.style.height='auto'; overlay.appendChild(imgEl); document.body.appendChild(overlay); } var imgEl=document.getElementById('avatar-overlay-img'); imgEl.src=s; overlay.style.display='flex'; }" .
                        '},false);})();' .
                        '</script>';
                },
                ''
            ),
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
                'Lolos ke Tahap 5 Besar' => 'yellow',
                'Lolos ke Tahap Wawancara' => 'purple',
                'Lolos ke Tahap Final' => 'teal',
            ])->addClass('large')->sortable(),

            MyRestfulButton::make('modules::pendaftar')->withoutEdit(),
        ];
    }

    public function filters(): array
    {
        return [
            new KategoriFilter,
            new StatusFilter,
        ];
    }
}
