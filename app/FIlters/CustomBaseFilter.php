<?php

namespace App\Filters;

use Illuminate\Support\Str;

abstract class CustomBaseFilter
{
    protected string $label = '';

    abstract public function render(): string;

    public function key(): string
    {
        return Str::kebab((new \ReflectionClass($this))->getShortName());
    }

    protected function label(): string
    {
        return $this->label;
    }
}
