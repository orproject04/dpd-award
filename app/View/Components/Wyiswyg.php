<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Wyiswyg extends Component
{
    public $id = '';
    public $name = '';
    public $label = '';
    public $height = '300';
    public $readonly = false;

    /**
     * Create a new component instance.
     */
    public function __construct(string $id = '', ?string $name = null, ?string $label = null, ?int $height = null, $readonly = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
        $this->height = $height;
        $this->readonly = $readonly;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.wyiswyg');
    }
}
