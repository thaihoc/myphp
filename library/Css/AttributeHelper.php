<?php

namespace Nth\Css;

use Nth\Css\Attribute;
use Nth\Html\AbstractAttributes;

class AttributeHelper extends AbstractAttributes {

    public function toString() {
        if (empty($this->attrs)) {
            return '';
        }
        $string = [];
        foreach ($this->attrs as $name => $value) {
            $attr = new Attribute($name, $value);
            array_push($string, $attr->toString());
        }
        return implode(';', array_filter($string));
    }

}