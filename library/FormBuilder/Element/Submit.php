<?php

namespace Nth\FormBuilder\Element;

use Nth\FormBuilder\Element\AbstractElement;
use Nth\FormBuilder\Element\ElementInterface;

class Submit extends AbstractElement implements ElementInterface {
    
    public function __construct($name = Element::SUBMIT, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::SUBMIT, $name, $attrs, $options, $order, $groups);
    }

    public function getControlNodeName() {
        return 'input';
    }

    public function toString() {
        $this->setAttr('type', 'submit');
        $this->setOption('controlOnly', true);
        return parent::toString();
    }

    public function getValue() {
        return $this->getAttr('value');
    }

    public function setValue($value) {
        $this->setAttr('value', $value);
        return $this;
    }

}
