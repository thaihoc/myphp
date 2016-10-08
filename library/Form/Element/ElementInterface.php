<?php

namespace Nth\Form\Element;

use Nth\Form\Group\AbstractGroup;

interface ElementInterface {

    public function getAttr($name, $def = null);

    public function setAttr($name, $value);

    public function setAttrs($attrs);

    public function getAttrs();

    public function getType();

    public function setType($type);

    public function getValue();

    public function setValue($value);

    public function getGroups();

    public function setGroups(array $groups);
    
    public function joinGroup(AbstractGroup $group);
    
    public function leaveGroup($name);
    
    public function getGroup($name, $def = null);
    
    public function memberOfGroup($name);

    public function getOrder();

    public function setOrder($order);
    
    public function getName();
    
    public function setName($name);
    
    public function getOption($name, $def = null);
    
    public function setOption($name, $value);
    
    public function containsAttr($name);
    
    public function removeAttr($name);
    
    public function containsOption($name);

    public function toString();
}
