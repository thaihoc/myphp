<?php

namespace Nth\Html;

interface AttributeInterface {

    public function getName();

    public function getValue();

    public function setName($name);

    public function setValue($name);
    
    public function toString();
}
