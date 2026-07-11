<?php

namespace App\View\Components;

use Laravolt\Suitable\Columns\Label;

class MyLabel extends Label
{
    public function cell($cell, $collection, $loop)
    {
        $label = data_get($cell, $this->field);

        if ($label !== null) {
            $class = implode(' ', $this->labelClass);

            foreach (($this->labelClassIf[$label] ?? []) as $additionalClass) {
                $class .= " $additionalClass";
            }

            return sprintf('<div class="ui label %s">%s</div>', $class, $label);
        }

        return '-';
    }
}
