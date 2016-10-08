<?php

namespace Nth\Form\Element;

use Nth\Form\Group\AbstractGroup;

abstract class AbstractElement {
    
    private $type;
    private $name;
    private $attrs;
    private $options;
    private $order;
    private $groups;

    public function __construct($type, $name, $attrs = [], $options = [], $order = 0, $groups = []) {
        $this->type = $type;
        $this->name = $name;
        $this->attrs = array_merge(['id' => $name, 'name' => $name], $attrs);
        $this->options = $options;
        $this->order = $order;
        $this->groups = $groups;
    }

    public function getType() {
        return $this->type;
    }

    public function getName() {
        return $this->name;
    }

    public function getAttrs() {
        return $this->attrs;
    }

    public function getAttr($name, $def = null) {
        return isset($this->attrs[$name]) ? $this->attrs[$name] : $def;
    }

    public function getOptions() {
        return $this->options;
    }
    
    public function getOption($name, $def = null) {
        return isset($this->options[$name]) ? $this->options[$name] : $def;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getGroups() {
        return $this->groups;
    }
    
    public function getGroup($name, $def = null) {
        return isset($this->groups[$name]) ? $this->groups[$name] : $def;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setAttrs($attrs) {
        $this->attrs = $attrs;
        return $this;
    }

    public function setAttr($name, $value) {
        $this->attrs[$name] = $value;
        return $this;
    }

    public function setOptions($options) {
        $this->options = $options;
        return $this;
    }
    
    public function setOption($name, $value) {
        $this->options[$name] = $value;
        return $this;
    }

    public function setOrder($order) {
        $this->order = $order;
        return $this;
    }

    public function setGroups(array $groups) {
        $this->groups = $groups;
        return $this;
    }
    
    public function joinGroup(AbstractGroup $group) {
        $name = $group->getName();
        if ($this->memberOfGroup($name)) {
            $this->groups[$name] = $group;
        }
        return $this;
    }
    
    public function leaveGroup($name) {
        unset($this->groups[$name]);
        return $this;
    }
    
    public function memberOfGroup($name) {
        return isset($this->groups[$name]);
    }
    
    public function containsAttr($name) {
        return isset($this->attrs[$name]);
    }
    
    public function removeAttr($name) {
        if ($this->containsAttr($name)) {
            unset($this->attrs[$name]);
        }
        return $this;
    }
    
    public function containsOption($name) {
        return isset($this->options[$name]);
    }
    
    abstract public function toString();

}
