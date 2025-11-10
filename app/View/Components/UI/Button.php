<?php

namespace App\View\Components\UI;

use Illuminate\View\Component;

class Button extends Component
{
    public $label;
    public $icon;
    public $href;
    public $color;
    public $outline;
    public $type;
    public $class;

    public function __construct(
        $label = '',
        $icon = null,
        $href = null,
        $color = 'blue',
        $outline = false,
        $type = 'button',
        $class = ''
    ) {
        $this->label = $label;
        $this->icon = $icon;
        $this->href = $href;
        $this->color = $color;
        $this->outline = filter_var($outline, FILTER_VALIDATE_BOOLEAN);
        $this->type = $type;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.ui.button');
    }
}
