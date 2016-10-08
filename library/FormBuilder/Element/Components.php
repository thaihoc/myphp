<?php

namespace Nth\FormBuilder\Element;

use Nth\FormBuilder\Element\Component\Wrapper;
use Nth\FormBuilder\Element\Component\Label;
use Nth\FormBuilder\Element\Component\Control;

class Components {

    private $control;
    private $wrapper;
    private $label;

    public function __construct($element) {
        $this->wrapper = new Wrapper($element);
        $this->label = new Label($element);
        $this->control = new Control($element);
    }

    public function getControl() {
        return $this->control;
    }

    public function getWrapper() {
        return $this->wrapper;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setControl($control) {
        $this->control = $control;
    }

    public function setWrapper($wrapper) {
        $this->wrapper = $wrapper;
    }

    public function setLabel($label) {
        $this->label = $label;
    }

}
