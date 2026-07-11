<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{

    public $id = '';
    public $size = '';
    public $title = '';
    public $content = '';

    public function __construct(?string $id = null, ?string $size = null, ?string $title = null, ?string $content = null)
    {
        $this->id = $id ?: 'modal-' . uniqid();
        $this->size = $size ?: 'md';
        $this->title = $title ?: 'Modal Title';
        $this->content = $content ?: 'Modal Content';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
