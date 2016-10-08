<?php

namespace Nth\Db\Oracle;

use Nth\Db\Oracle\AbstractParameters;
use Nth\Db\Oracle\Parameter;
use Nth\Db\Oracle\OracleFunction;
use Nth\Helper\Convertor;
use Zend\Stdlib\ArrayObject;

class Parameters extends AbstractParameters {
    
    const SERIALIZE_DEFAULT = 0;
    const SERIALIZE_INSERT = 1;
    const SERIALIZE_UPDATE = 2;

    public static function remap($parameters) {
        $result = [];
        if (is_string($parameters)) {
            $parameters = explode(',', $parameters);
        } elseif ($parameters instanceof ArrayObject) {
            $parameters = $parameters->getArrayCopy();
        }
        if (count($parameters)) {
            foreach ($parameters as $name => $value) {
                if (is_numeric($name) && is_string($value)) {
                    $parameter = Parameter::fromString($value);
                } else {
                    $parameter = new Parameter($name, $value);
                }
                $result[$parameter->getName()] = $parameter;
            }
        }
        return $result;
    }

    public function setParameters($parameters) {
        parent::setParameters(self::remap($parameters));
        return $this;
    }

    public function &getParameter($name) {
        if (!$this->existsParameter($name)) {
            $this->setParameter($name, new Parameter($name));
        }
        return parent::getParameter($name);
    }

    public function bindValues($data) {
        $iterator = Convertor::toArrayObject($data)->getIterator();
        while ($iterator->valid()) {
            $name = $iterator->key();
            if ($this->existsParameter($name)) {
                $this->getParameter($name)->setValue($iterator->current());
            }
            $iterator->next();
        }
        return $this;
    }
    
    public function toString($serializeType = self::SERIALIZE_DEFAULT, $glue = ', ') {
        $array = [];
        $iterator = $this->toArrayObject()->getIterator();
        while ($iterator->valid()) {
            $parameter = $iterator->current();
            $name = $parameter->getName();
            if (self::SERIALIZE_DEFAULT === $serializeType && !OracleFunction::isResultParameter($name)) {
                array_push($array, $parameter->toString());
            } elseif (self::SERIALIZE_INSERT === $serializeType) {
                array_push($array, $parameter->toString());
            } elseif (self::SERIALIZE_UPDATE === $serializeType) {
                array_push($array, $name . ' = ' . $parameter->toString());
            }
            $iterator->next();
        }
        return implode($glue, $array);
    }

    public function toArrayObjectValue() {
        return Convertor::toArrayObject($this->toArrayValue());
    }

    public function toArrayValue() {
        $array = [];
        $iterator = $this->toArrayObject()->getIterator();
        while ($iterator->valid()) {
            $parameter = $iterator->current();
            $array[$parameter->getName()] = $parameter->getValue();
            $iterator->next();
        }
        return $array;
    }

    public function addPrefix() {
        $iterator = $this->toArrayObject()->getIterator();
        while ($iterator->valid()) {
            $parameter = $iterator->current();
            $this->getParameter($parameter->getName())->addPrefix();
            $iterator->next();
        }
        return $this;
    }

    public function removePrefix() {
        $iterator = $this->toArrayObject()->getIterator();
        while ($iterator->valid()) {
            $parameter = $iterator->current();
            $this->getParameter($parameter->getName())->removePrefix();
            $iterator->next();
        }
        return $this;
    }

}
