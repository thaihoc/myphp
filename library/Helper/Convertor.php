<?php

namespace Nth\helper;

use Zend\Stdlib\ArrayObject;

class Convertor {
    
    public static function toArrayObject($var){
        if($var instanceof ArrayObject){
            return $var;
        }
        return new ArrayObject((array) $var, ArrayObject::ARRAY_AS_PROPS);
    }
    
}