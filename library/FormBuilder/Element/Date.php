<?php

namespace Nth\FormBuilder\Element;

use Nth\FormBuilder\Element\AbstractElement;
use Nth\FormBuilder\Element\ElementInterface;

class Date extends AbstractElement implements ElementInterface {
    
    public function __construct($name, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::DATE, $name, $attrs, $options, $order, $groups);
    }

    public function getControlNodeName() {
        return 'input';
    }

    public function toString() {
        $this->setAttr('type', 'text');
        $control = $this->getComponents()->getControl()->getNode();
        $control->setContent(false);
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
