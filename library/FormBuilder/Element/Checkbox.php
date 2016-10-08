<?php

namespace Nth\FormBuilder\Element;

use Nth\FormBuilder\Element\AbstractElement;
use Nth\FormBuilder\Element\ElementInterface;

class Checkbox extends AbstractElement implements ElementInterface {
    
    public function __construct($name, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::CHECKBOX, $name, $attrs, $options, $order, $groups);
    }

    public function getControlNodeName() {
        return 'input';
    }

    public function toString() {
        $checkedAttr = false;
        $checkedValue = $this->getOption('checked');
        if ((is_bool($checkedValue) && $checkedValue) || $checkedValue == $this->getValue()) {
            $checkedAttr = 'true';
        }
        $this->setAttr('checked', $checkedAttr);
        $this->setAttr('type', 'checkbox');
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
    
    public function setChecked($value) {
        $this->setOption('checked', $value);
        return $this;
    }
    
    public function getChecked() {
        return $this->getOption('checked');
    }

}
