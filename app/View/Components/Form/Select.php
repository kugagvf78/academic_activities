<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Select extends Component
{
    public $name;
    public $label;
    public $options;
    public $selected;
    public $placeholder;
    public $class;

    public function __construct(
        $name,
        $label = null,
        $options = [],
        $selected = '',
        $placeholder = 'Chọn...',
        $class = ''
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = $selected ?? request($name);
        $this->placeholder = $placeholder;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.form.select');
    }
}
