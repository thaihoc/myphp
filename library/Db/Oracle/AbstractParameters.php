<?php

namespace Nth\Db\Oracle;

use Nth\Db\Oracle\AbstractParameter;
use Nth\Helper\Convertor;
use Zend\Stdlib\ArrayObject;

abstract class AbstractParameters extends AbstractParameter {

    private $parameters;

    public function __construct($parameters = [], $prefix = 'P_', $prefixMode = self::PREFIX_NOT_YET) {
        $this->setParameters($parameters);
        parent::__construct($prefix, $prefixMode);
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function setParameters($parameters) {
        if ($parameters instanceof ArrayObject) {
            $parameters = $parameters->getArrayCopy();
        }
        $this->parameters = $parameters;
        return $this;
    }

    public function setParameter($name, AbstractParameter $parameter) {
        $this->parameters[$name] = $parameter;
        return $this;
    }

    public function &getParameter($name) {
        $parameter = null;
        if ($this->existsParameter($name)) {
            return $this->parameters[$name];
        }
        return $parameter;
    }

    public function existsParameter($name) {
        return isset($this->parameters[$name]);
    }
    
    public function count() {
        return count($this->parameters);
    }

    public function toArrayObject() {
        return Convertor::toArrayObject($this->parameters);
    }

    abstract public function bindValues($data);
    
    abstract public function toArrayObjectValue();

    abstract public function toArrayValue();

    abstract public function toString();
}
