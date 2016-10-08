<?php

namespace Nth\FormBuilder\Element;

use Nth\Form\Element\AbstractElement as AbstractBaseElement;
use Nth\FormBuilder\Element\Components;

abstract class AbstractElement extends AbstractBaseElement {

    private $components;

    public function __construct($type, $name, $attrs = [], $options = [], $order = 0, $groups = []) {
        $this->components = new Components($this);
        parent::__construct($type, $name, $attrs, $options, $order, $groups);
    }

    public function getComponents() {
        return $this->components;
    }

    public function setComponents($components) {
        $this->components = $components;
    }

    public function toString() {
        $components = $this->getComponents();
        if ($this->getOption('controlOnly')) {
            return $components->getControl()->getNode()->toString();
        }
        $control = $components->getControl()->getNode();
        $wrapper = $components->getWrapper()->getNode();
        $wrapper->prependChild($control);
        if ($this->getOption('showLabel', true)) {
            $label = $components->getLabel()->getNode();
            $wrapper->prependChild($label);
        }
        return $wrapper->toString();
    }

    abstract function getControlNodeName();
}
