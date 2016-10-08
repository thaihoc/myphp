<?php

namespace Nth\Html;

use Nth\Html\AttributesInterface;

abstract class AbstractAttributes implements AttributesInterface {

    protected $attrs;

    public function __construct(array $attrs = []) {
        $this->attrs = $attrs;
    }

    public function getAttrs() {
        return $this->attrs;
    }

    public function setAttrs(array $attrs) {
        $this->attrs = $attrs;
        return $this;
    }
    
    public function mergeAttrs(array $attrs) {
        $this->attrs = array_merge($attrs, $this->attrs);
        return $this;
    }

    public function getAttr($name, $def = null) {
        return $this->containsAttr($name) ? $this->attrs[$name] : $def;
    }

    public function setAttr($name, $value) {
        $this->attrs[$name] = $value;
        return $this;
    }

    public function containsAttr($name) {
        return isset($this->attrs[$name]);
    }

    abstract public function toString();
}
