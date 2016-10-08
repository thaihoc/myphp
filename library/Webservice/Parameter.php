<?php

namespace Nth\Webservice;

use stdClass;

class Parameter {
    
    private $parameter;
    
    public function __construct($parameter = null) {
        $this->parameter = $parameter;
    }
    
    public function getParameter() {
        return $this->parameter;
    }

    public function setParameter($parameter) {
        $this->parameter = $parameter;
        return $this;
    }
    
    public function toArray() {
        $parameter = $this->getParameter();
        if (is_string($parameter)) {
            $parameter = json_decode($parameter);
            if (is_null($parameter)) {
                return [$this->getParameter()];
            }
        }
        if (is_array($parameter) && isset($parameter[0]) && $parameter[0] instanceof stdClass) {
            $result = [];
            for ($i = 0; $i < count($parameter); $i++) {
                $obj = $parameter[$i];
                $result[$obj->key] = $obj->value;
            }
            return $result;
        }
        return $parameter;
    }
    
}