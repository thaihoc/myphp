<?php

namespace Nth\FormBuilder\Group;

use Nth\FormBuilder\Element\Element;
use Nth\FormBuilder\Group\AbstractGroup;
use Nth\FormBuilder\Group\GroupInterface;

class Group extends AbstractGroup implements GroupInterface {

    public function __construct($name, array $elements = array(), $data = array()) {
        parent::__construct($name, Element::createList($elements), $data);
        $this->bindValues();
    }

}
