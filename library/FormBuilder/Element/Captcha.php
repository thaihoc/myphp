<?php

namespace Nth\FormBuilder\Element;

use Nth\FormBuilder\Element\ElementInterface;
use Nth\FormBuilder\Element\AbstractElement;

class Captcha extends AbstractElement implements ElementInterface {
    
    public function __construct($name, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::CAPTCHA, $name, $attrs, $options, $order, $groups);
    }

    public function getValue() {
        return $this->getAttr('value');
    }

    public function setValue($value) {
        $this->setAttr('value', $value);
        return $this;
    }

    public function toString() {
        $this->setAttr('type', 'text');
        $control = $this->getComponents()->getControl()->getNode();
        $control->setContent(false);
        return parent::toString();
    }

    public function getControlNodeName() {
        return 'input';
    }

}
