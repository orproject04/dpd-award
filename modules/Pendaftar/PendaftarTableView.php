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
                    $src = asset('assets/images/default.png');
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
                return "<span style='display:block;text-align:center;'>" . $data->nomor_registrasi . "</span>";
            }, 'Nomor Registrasi')->sortable('nomor_registrasi'),
            Raw::make(function ($data) {
                return "<span style='display:block;text-align:center;'>" . $data->kategori . "</span>";
            }, 'Kategori')->sortable('kategori'),
            MyLabel::make('status')->map([
                'Diajukan' => 'blue',
                'Lolos Verifikasi Berkas' => 'yellow',
                'Lolos Penilaian Tahap 1' => 'yellow',
                'Lolos Penilaian Tahap 2' => 'yellow',
                'Lolos Penilaian Tahap 3' => 'yellow',
                'Lolos Tahap Wawancara' => 'yellow',
                'Lolos Tahap Final' => 'teal',
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
