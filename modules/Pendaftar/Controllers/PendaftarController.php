<?php

namespace Modules\Pendaftar\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Pendaftar\Models\Pendaftar;
use Modules\Pendaftar\PendaftarTableView;
use Modules\Pendaftar\Requests\Store;
use Modules\Pendaftar\Requests\Update;

class PendaftarController extends Controller
{
    public function index()
    {
        return PendaftarTableView::make()->view('pendaftar::index')->showPerPage();
    }

    public function create(): View
    {
        /** @var view-string */
        $view = 'pendaftar::create';

        return view($view);
    }

    public function store(Store $request): RedirectResponse
    {
        Pendaftar::create($request->validated());

        return to_route('modules::pendaftar.index')->withSuccess('Pendaftar saved');
    }

    public function show(Pendaftar $pendaftar): View
    {
        /** @var view-string $view */
        $view = 'pendaftar::show';

        return view($view, compact('pendaftar'));
    }

    public function edit(Pendaftar $pendaftar): View
    {
        /** @var view-string $view */
        $view = 'pendaftar::edit';

        return view($view, compact('pendaftar'));
    }

    public function update(Update $request, Pendaftar $pendaftar): RedirectResponse
    {
        $pendaftar->update($request->validated());

        return to_route('modules::pendaftar.index')->withSuccess('Pendaftar updated');
    }

    public function destroy(Pendaftar $pendaftar): RedirectResponse
    {
        $pendaftar->delete();

        return to_route('modules::pendaftar.index')->withSuccess('Pendaftar deleted');
    }
}
