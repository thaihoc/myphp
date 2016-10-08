<?php

namespace Nth\Html;

interface AttributesInterface {

    public function getAttrs();
    
    public function getAttr($name, $def = null);

    public function setAttr($name, $value);

    public function setAttrs(array $attrs);
}
