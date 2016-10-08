<?php

namespace Nth\FormBuilder\Element;

use Nth\FormBuilder\Element\Element;
use Nth\FormBuilder\Element\AbstractElement;
use Nth\FormBuilder\Element\ElementInterface;
use Nth\Html\Node;
use Nth\Helper\Convertor;

class Select extends AbstractElement implements ElementInterface {
    
    public function __construct($name, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::SELECT, $name, $attrs, $options, $order, $groups);
    }

    public function toString() {
        $content = $this->getSelectItems();
        $selected = $this->getSelected();
        if (!$content && !is_null($selected)) {
            $this->setAttr('data-fb-selected', $selected);
        }
        $control = $this->getComponents()->getControl()->getNode();
        $control->setContent($content);
        return parent::toString();
    }

    public function getSelected() {
        if ($this->containsOption('selected')) {
            return $this->getOption('selected');
        }
        return $this->getBindOption()->offsetGet('selected');
    }

    public function getSelectItems() {
        if ($this->containsOption('innerHtml')) {
            return $this->getOption('innerHtml');
        }
        return $this->bindSelectItem();
    }

    private function getBindOption() {
        $option = $this->getOption('bindOptions', []);
        return Convertor::toArrayObject($option);
    }
    
    public function setBindOption($name, $value) {
        $bindOptions = $this->getOption('bindOptions', []);
        $bindOptions[$name] = $value;
        $this->setOption('bindOptions', $bindOptions);
        return $this;
    }

    private function bindSelectItem() {
        $option = $this->getBindOption();
        $result = $this->getEmptyItem();
        $data = Convertor::toArrayObject($option->data);
        $iterator = $data->getIterator();
        while ($iterator->valid()) {
            $content = $this->getItemData($iterator, $option->name);
            $value = $this->getItemData($iterator, $option->value);
            $item = new Node('option', $content, [
                'value' => $value,
                'selected' => $this->isSelected($value)
            ]);
            $result .= $item->toString();
            $iterator->next();
        }
        return $result;
    }

    public function getEmptyItem() {
        $option = $this->getBindOption();
        $content = $option->offsetGet('emptyOption');
        if (empty($content)) {
            return null;
        }
        $node = new Node('option', $content, ['value' => null]);
        return $node->toString();
    }

    private function getItemData($iterator, $offset) {
        $row = $iterator->current();
        if (!(is_object($row) || is_array($row))) {
            return $iterator->current();
        }
        $item = Convertor::toArrayObject($row);
        if (is_callable($offset)) {
            return call_user_func($offset, $row);
        }
        return $item->offsetGet($offset);
    }

    protected function isSelected($value) {
        $selected = $this->getSelected();
        if (is_array($selected)) {
            return in_array($value, $selected);
        }
        return $selected == $value;
    }

    public function getControlNodeName() {
        return 'select';
    }

    public function setSelected($selected) {
        $this->setOption('selected', $selected);
        return $this;
    }

    public function getValue() {
        return $this->getSelected();
    }

    public function setValue($value) {
        return $this->setSelected($value);
    }

}
