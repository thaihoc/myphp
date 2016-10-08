<?php

namespace Nth\FormBuilder\DataType;

use Zend\Stdlib\ArrayObject;

class ListType extends ArrayObject {

    public function __construct($value = null) {
        if (is_string($value)) {
            $value = json_decode($value);
        }
        if (!is_array($value)) {
            $value = [];
        }
        parent::__construct($value, ArrayObject::ARRAY_AS_PROPS);
    }
    
    public function isEmpty() {
        return count($this->storage) === 0;
    }
    
}