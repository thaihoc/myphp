<?php

namespace Nth\Form\Group;

use Zend\Filter\Word\CamelCaseToDash;
use ReflectionClass;
use Exception;
use Nth\File\File;
use Nth\Form\Group\GroupInterface;
use Nth\Html\AbstractAttributes;
use Nth\Html\Node;

abstract class AbstractGroup extends AbstractAttributes {
    
    private $name;
    private $elements;
    private $data;

    public function __construct($name, array $elements = [], $data = [], array $attrs = []) {
        $this->name = $name;
        $this->elements = $elements;
        $this->data = $data;
        parent::__construct($attrs);    
    }

    public function appendElement($element) {
        array_push($this->elements, $element);
        return $this;
    }

    public function count() {
        return count($this->elements);
    }

    public function existsElement($name) {
        return !is_null($this->get($name));
    }

    public function get($name) {
        return $this->getElement($name);
    }

    public function add($element) {
        if ($this->existsElement($element->getName())) {
            return $this;
        }
        return $this->appendElement($element);
    }

    public function mergeWith() {
        $arguments = func_get_args();
        if (count($arguments) < 1) {
            throw new Exception('Cannot call ' . __FUNCTION__ . ' method without arguments');
        }
        foreach ($arguments as $group) {
            if (!$group instanceof AbstractGroup) {
                throw new Exception('The every argument pass in ' . __FUNCTION__ . ' method must be an instanceof AbstractGroup');
            }
            $this->mergeElements($group->getElements());
        }
        return $this;
    }

    public function mergeElements(array $elements) {
        if (count($elements) < 1) {
            return $this;
        }
        foreach ($elements as $element) {
            if ($this->existsElement($element->getName())) {
                continue;
            }
            $this->appendElement($element);
        }
        return $this;
    }

    public function getElement($name) {
        if ($this->count()) {
            foreach ($this->getElements() as &$element) {
                if ($element->getName() == $name) {
                    return $element;
                }
            }
        }
        return null;
    }

    public function getElements() {
        return $this->elements;
    }

    public function getName() {
        return $this->name;
    }

    public function prependElement($element) {
        array_unshift($this->elements, $element);
        return $this;
    }

    public function removeElement($name) {
        $element = $this->get($name);
        unset($element);
        return $this;
    }

    public function setElements(array $elements) {
        $this->elements = $elements;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function sort($mode = 'ESC') {
        if ($this->count()) {
            $elements = array();
            foreach ($this->getElements() as $element) {
                $elements[$element->getOrder()] = $element;
            }
            $mode === GroupInterface::ESC ? ksort($elements) : krsort($elements);
            $this->setElements($elements);
        }
        return this;
    }

    public function toString() {
        $fieldset = new Node('fieldset', null, $this->getAttrs());
        if ($this->count()) {
            foreach ($this->getElements() as $element) {
                $fieldset->appendContent($element->toString());
            }
        }
        return $fieldset->toString();
    }

    public function getDefaultConfig() {
        $filter = new CamelCaseToDash();
        $reflection = new ReflectionClass(get_called_class());
        $basename = $filter->filter($reflection->getShortName()) . '.php';
        $directory = dirname($reflection->getFileName()) . File::getDirectorySeparator() . 'config';
        $file = new File($directory . File::getDirectorySeparator() . $basename);
        if (!$file->exists()) {
            throw new Exception('The configruration file does not exists in the path: ' . $file->getPath());
        }
        return require $file->getPath();
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function getData($offset = null, $def = null) {
        if (is_null($offset)) {
            return $this->data;
        }
        return isset($this->data[$offset]) ? $this->data[$offset] : $def;
    }

    public function bindValues() {
        if (count($this->data) < 1 || $this->count() < 1) {
            return $this;
        }
        foreach ($this->getElements() as &$element) {
            $value = $this->getData($element->getName());
            $element->setValue($value);
        }
        return $this;
    }

}
