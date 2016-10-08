<?php

namespace Nth\Db\Oracle;

abstract class AbstractParameter {

    const PREFIX_ALL = 1;
    const PREFIX_NOT_YET = 2;

    private $prefix;
    private $prefixMode;

    public function __construct($prefix = 'P_', $prefixMode = self::PREFIX_NOT_YET) {
        $this->prefix = $prefix;
        $this->prefixMode = $prefixMode;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    public function getPrefixMode() {
        return $this->prefixMode;
    }

    public function setPrefix($prefix) {
        $this->prefix = $prefix;
        return $this;
    }

    public function setPrefixMode(int $prefixMode) {
        $this->prefixMode = $prefixMode;
        return $this;
    }
    
    abstract public function addPrefix();
    
    abstract public function removePrefix();

}
