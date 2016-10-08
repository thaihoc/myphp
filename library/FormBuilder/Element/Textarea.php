<?php

namespace Nth\FormBuilder\Control;

use Nth\FormBuilder\Element\AbstractElement;
use Nth\FormBuilder\Element\ElementInterface;

class Textarea extends AbstractElement implements ElementInterface {
    
    public function __construct($name, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::TEXTAREA, $name, $attrs, $options, $order, $groups);
    }

    public function getControlNodeName() {
        return 'textarea';
    }

    public function getValue() {
         return $this->getOption('innerHtml');
    }

    public function setValue($value) {
        $this->setOption('innerHtml', $value);
        return $this; 
    }

}
