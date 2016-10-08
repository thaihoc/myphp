<?php

namespace Nth\FormBuilder\Element\Component;

use Nth\FormBuilder\Element\AbstractElement;

abstract class AbstractComponent {

    private $element;

    public function __construct(AbstractElement $element) {
        $this->element = $element;
    }

    public function getElement() {
        return $this->element;
    }

    public function setElement(AbstractElement $element) {
        $this->element = $element;
    }

}
