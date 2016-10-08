<?php

namespace Nth\FormBuilder\Element\Component;

use Nth\Html\Node;
use Nth\FormBuilder\Element\Component\AbstractComponent;
use Nth\FormBuilder\Element\Component\ComponentInterface;

class Control extends AbstractComponent implements ComponentInterface {
    
    private $node;

    public function getAttrs() {
        return $this->getElement()->getAttrs();
    }

    public function getNode() {
        if (!$this->node instanceof Node) {
            $this->node = new Node($this->getElement()->getControlNodeName(), $this->getElement()->getValue());
        }
        return $this->node->setAttrs($this->getAttrs());
    }

}
