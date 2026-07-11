<?php

namespace App\View\Components;

use Laravolt\Suitable\Columns\RestfulButton;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MyRestfulButton extends RestfulButton
{

    public $withTextDetail = false;
    public $withoutDetail = false;
    public $withoutEdit = false;

    public function cell($data, $collection, $loop)
    {
        $actions = $this->buildActions($data);

        if ($this->withoutDetail) {
            $this->withTextDetail = false;
            unset($actions['show']);
        }

        if ($this->withoutEdit) {
            unset($actions['edit']);
        }

        $deleteConfirmation = $this->buildDeleteConfirmation($data);
        $key = Str::kebab(get_class($data)) . '-' . $data->getKey();
        $withTextDetail = $this->withTextDetail;

        return View::make('columns.restful_button', compact('data', 'actions', 'deleteConfirmation', 'key', 'withTextDetail'))
            ->render();
    }

    protected function buildActions($data)
    {
        $actions = ['show', 'edit', 'destroy'];

        if ($this->withoutDetail) {
            $actions = ['edit', 'destroy'];
        }

        $actions = collect($actions)
            ->reject(
                function ($action) {
                    return ! in_array($action, $this->buttons);
                }
            )->reject(function ($action) use ($data) {
                if (Auth::user() && Auth::user()->hasPermission('*')) {
                    return false;
                }

                $policyEnabled = Gate::getPolicyFor(get_class($data)) !== null;

                return $policyEnabled && Auth::user()->cannot($action, $data);
            })
            ->mapWithKeys(function ($action) use ($data) {
                return [$action => $this->getRoute($action, $this->routeParameters + [$data->getRouteKey()])];
            });

        return $actions;
    }

    public function withTextDetail()
    {
        $this->withTextDetail = true;

        return $this;
    }

    public function withoutDetail()
    {
        $this->withoutDetail = true;

        return $this;
    }

    public function withoutEdit()
    {
        $this->withoutEdit = true;

        return $this;
    }
}
