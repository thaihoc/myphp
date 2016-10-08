<?php

namespace Nth\FormBuilder\Element;

use Nth\FormBuilder\Element\AbstractElement;
use Nth\FormBuilder\Element\ElementInterface;

class Button extends AbstractElement implements ElementInterface {
    
    public function __construct($name, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::BUTTON, $name, $attrs, $options, $order, $groups);
    }

    public function getControlNodeName() {
        return 'button';
    }

    public function toString() {
        if (!$this->containsAttr('type')) {
            $this->setAttr('type', 'button');
        }
        $this->setOption('controlOnly', true);
        return parent::toString();
    }

    public function getValue() {
        return $this->getOption('innerHtml');
    }

    public function setValue($value) {
        $this->setOption('innerHtml', $value);
        return $this;
    }

}
