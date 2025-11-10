<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Input extends Component
{
    public $name;
    public $type;
    public $label;
    public $value;
    public $placeholder;
    public $class;
    public $disabled;

    public function __construct(
        $name,
        $type = 'text',
        $label = null,
        $value = null,
        $placeholder = '',
        $class = '',
        $disabled = false
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->value = $value ?? old($name, request($name));
        $this->placeholder = $placeholder;
        $this->class = $class;
        $this->disabled = filter_var($disabled, FILTER_VALIDATE_BOOLEAN);
    }

    public function render()
    {
        return view('components.form.input');
    }
}
