<?php

namespace App\Suitable;

use Laravolt\Suitable\Builder as BaseBuilder;

class CustomBuilder extends BaseBuilder
{
    protected $filters = [];
    protected $perYear = false;

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function setPerYear(bool $perYear = false)
    {
        $this->perYear = $perYear;
        return $this;
    }

    public function perYear(): bool
    {
        return $this->perYear;
    }
}
