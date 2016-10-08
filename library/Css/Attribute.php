<?php

namespace Nth\Css;

use Nth\Html\AttributeInterface;

class Attribute implements AttributeInterface {
    
    private $name;
    private $value;
    
    public function __construct($name, $value) {
        $this->name = $name;
        $this->value = $value;
    }
    
    public function getName() {
        return $this->name;
    }

    public function getValue() {
        return $this->value;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setValue($value) {
        $this->value = $value;
    }
    
    public function toString(){
        if($this->value === false){
            return '';
        }
        return sprintf('%s:%s', $this->name, $this->value);
    }


    
}