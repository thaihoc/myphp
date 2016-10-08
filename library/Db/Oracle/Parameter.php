<?php

namespace Nth\Db\Oracle;

use Exception;
use Nth\Db\Oracle\AbstractParameter;
use Nth\Db\Oracle\Types;

class Parameter extends AbstractParameter {

    private $name;
    private $value;
    private $type;
    private $maxlength;
    private $format;
    private $modes;

    public function __construct($name = null, $value = null, $type = null, $maxlength = -1, $modes = 'IN OUT') {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
        $this->maxlength = $maxlength;
        $this->modes = $modes;
        $this->format = 'DD/MM/YYYY';
        parent::__construct();
    }

    public function getMaxlength() {
        return $this->maxlength;
    }

    public function setMaxlength($maxlength) {
        $this->maxlength = $maxlength;
    }
    
    public function getFormat() {
        return $this->format;
    }

    public function setFormat($format) {
        $this->format = $format;
    }

    public function getName() {
        return $this->name;
    }

    public function &getValue() {
        return $this->value;
    }

    public function getType() {
        return $this->type;
    }

    public function getModes() {
        return $this->modes;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setModes($modes) {
        $this->modes = $modes;
        return $this;
    }

    public function bindTo(&$stmt) {
        $name = $this->getNameToBind();
        $type = $this->getTypeToBind();
        oci_bind_by_name($stmt, $name, $this->value, $this->maxlength, $type);
        return $this;
    }

    public static function fromString($string) {
        $parameter = new Parameter();
        $array = explode(' ', preg_replace('/[\s]+/', ' ', trim($string)));
        $count = count($array);
        if ($count === 4) {
            $parameter->setName(trim($array[0]))->setType(trim($array[3]))->setModes(trim($array[1]) . ' ' . trim($array[2]));
        } elseif ($count === 3) {
            $parameter->setName(trim($array[0]))->setType(trim($array[2]))->setModes(trim($array[1]));
        } elseif ($count === 2) {
            $parameter->setName(trim($array[0]))->setType(trim($array[1]))->setModes('IN');
        } elseif ($count === 1) {
            $parameter->setName(trim($array[0]));
        } else {
            throw new Exception('Cannot create Parameter object from ' . $string);
        }
        return $parameter;
    }
    
    public function getNameToBind() {
        return ':' . trim($this->name);
    }

    public function getTypeToBind() {
        if (Types::isInt($this->type) || Types::isFloat($this->type) || Types::isLong($this->type)) {
            return SQLT_INT;
        } elseif (Types::isString($this->type) || Types::isByte($this->type) || Types::isDate($this->type)) {
            return SQLT_CHR;
        } elseif (Types::isCursor($this->type)) {
            return OCI_B_CURSOR;
        } elseif (Types::isNclob($this->type) || Types::isClob($this->type) || Types::isBlob($this->type)) {
            return OCI_B_CLOB;
        } elseif (Types::isVarray($this->type)) {
            return OCI_B_NTY;
        } else {
            throw new Exception('Unknown type ' . $this->type . ' to bind parameter');
        }
    }

    public function addPrefix() {
        if (self::PREFIX_ALL === $this->getPrefixMode() || (self::PREFIX_NOT_YET === $this->getPrefixMode() && !$this->hasPrefix())) {
            $this->setName($this->getPrefix() . $this->getName());
        }
        return $this;
    }

    public function removePrefix() {
        if ($this->hasPrefix()) {
            $this->setName(substr($this->getName(), strlen($this->getPrefix())));
        }
        return $this;
    }
    
    public function hasPrefix() {
        return substr($this->getName(), 0, strlen($this->getPrefix())) == $this->getPrefix();
    }
    
    public function toString() {
        $name = $this->getNameToBind();
        if (Types::isDate($this->type)) {
            $name = sprintf("to_date(%s, '%s')", $name, $this->format);
        }
        return $name;
    }

}
