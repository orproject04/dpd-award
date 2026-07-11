<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $header;
    public $message;
    public $iconCenter = false;
    public $fullWidth = true;
    public $class = '';

    public function __construct(?string $type = null, ?string $header = null, ?string $message = null, $iconCenter = false, $fullWidth = true, $class = '')
    {
        $this->type = $type;
        $this->header = $header;
        $this->message = $message;
        $this->iconCenter = $iconCenter;
        $this->fullWidth = $fullWidth;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}
