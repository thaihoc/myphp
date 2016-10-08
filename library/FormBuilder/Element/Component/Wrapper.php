<?php

namespace Nth\FormBuilder\Element\Component;

use Nth\Html\Node;
use Nth\FormBuilder\Element\Component\AbstractComponent;
use Nth\FormBuilder\Element\Component\ComponentInterface;

class Wrapper extends AbstractComponent implements ComponentInterface {
    
    private $node;
    
    public function getAttrs() {
        $element = $this->getElement();
        $wrapperAttributes = $element->getOption('wrapperAttributes', []);
        return array_merge([
            'id' => '_cw_' . $element->getAttr('id'),
            'class' => 'eform ' . strtolower($element->getType())
        ], $wrapperAttributes);
    }

    public function getNode() {
        if (!$this->node instanceof Node) {
            $this->node = new Node('div');
        }
        return $this->node->setAttrs($this->getAttrs());
    }

}
