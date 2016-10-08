<?php

namespace Nth\Form;

use Nth\Form\Group\AbstractGroup;
use Nth\Form\Group\GroupInterface;

interface FormInterface extends GroupInterface {

    public function addGroup(AbstractGroup $group);

    public function getGroup($name);
    
    public function removeGroup($name);
    
    public function setGroups(array $groups);
    
    public function getGroups();

    public function existsGroup($name);

    public function openTag();
    
    public function closeTag();
    
    public function createElements(array $elements);
}
