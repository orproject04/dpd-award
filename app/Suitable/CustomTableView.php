<?php

// app/Suitable/CustomTableView.php
namespace App\Suitable;

use Laravolt\Suitable\TableView;

abstract class CustomTableView extends TableView
{
    public function render()
    {
        if ($this->search !== null) {
            $this->html->search($this->search);
        }

        $source = $this->getSource();

        // 🔁 Gunakan CustomBuilder langsung di sini
        $table = app('laravolt.suitable')->source($this->html->resolve($source));

        if (is_string($this->title) && $this->title !== '') {
            $table->title($this->title);
        }

        $table->showPerPage($this->showPerPage);

        $this->html->decorate($table);

        if (is_callable($this->decorateCallback)) {
            call_user_func($this->decorateCallback, $table);
        }

        collect($this->plugins)->each->decorate($table);

        $table->columns($this->html->filter($this->columns()));
        $table->setFilters($this->filters());

        return $table->render();
    }

    public function toResponse($request)
    {
        if ($this->search !== null) {
            $this->html->search($this->search);
        }

        $source = $this->getSource();
        $table = app('laravolt.suitable')->source($this->html->resolve($source));

        if (is_string($this->title) && $this->title !== '') {
            $table->title($this->title);
        }

        $table->showPerPage($this->showPerPage);

        // Start decorating table
        // 1. HTML decoration
        $this->html->decorate($table);

        // 2. User defined decoration
        if (is_callable($this->decorateCallback)) {
            call_user_func($this->decorateCallback, $table);
        }

        // 3. Plugin decoration
        collect($this->plugins)->each->decorate($table);

        foreach ($this->plugins as $plugin) {
            if ($plugin->shouldResponse()) {
                $table->columns($plugin->filter($this->columns()));

                return $plugin->response($source, $table);
            }
        }

        $table->columns($this->html->filter($this->columns()));
        $table->setFilters($this->filters());
        $table->setPerYear($this->perYear());

        return $this->html->response($source, $table);
    }

    public function filters() {}

    public function perYear() {
        return false;
    }
}
