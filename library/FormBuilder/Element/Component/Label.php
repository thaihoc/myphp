<?php

namespace Nth\FormBuilder\Element\Component;

use Nth\Html\Node;
use Nth\FormBuilder\Element\Component\AbstractComponent;
use Nth\FormBuilder\Element\Component\ComponentInterface;

class Label extends AbstractComponent implements ComponentInterface {
    
    private $node;
    
    public function getAttrs() {
        $element = $this->getElement();
        if ($element->getOption('label')) {
            return [];
        }
        return [
            'id' => '_lbl_' . $element->getAttr('id'),
            'for' => $element->getAttr('id')
        ];
    }

    public function getNode() {
        if (!$this->node instanceof Node) {
            $this->node = new Node('label');
        }
        $content = $this->getElement()->getOption('label');
        if ($this->marksMandatory()) {
            $mandatory = new Node('span', '(*)', ['class' => 'mandatory']);
            $content .= $mandatory->toString();
        }
        $this->node->setContent($content);
        return $this->node->setAttrs($this->getAttrs());
    }
    
    public function marksMandatory() {
        $element = $this->getElement();
        return $element->getOption('marksMandatory', (bool) $element->getAttr('valid-required'));
    }

}
