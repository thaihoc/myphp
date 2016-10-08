<?php

namespace Nth\Form;

use Nth\Form\Group\AbstractGroup;
use Nth\Html\Node;

abstract class AbstractForm extends AbstractGroup {

    public static $defaultAttrs = array(
        'id' => 'mainForm',
        'name' => 'mainForm',
        'method' => 'POST',
        'action' => ''
    );
    private $groups;

    public function __construct(array $elements = [], $data = [], array $attrs = []) {
        parent::__construct(get_called_class(), $elements, $data, self::$defaultAttrs + $attrs);
    }

    public function getGroups() {
        return $this->groups;
    }

    public function setGroups(array $groups) {
        $this->groups = $groups;
        return $this;
    }

    public function getGroup($name) {
        return isset($this->groups[$name]) ? $this->groups[$name] : null;
    }

    public function addGroup(AbstractGroup $group) {
        $this->groups[$group->getName()] = $group;
    }

    public function removeGroup($name) {
        unset($this->groups[$name]);
        return $this;
    }

    public function existsGroup($name) {
        return isset($this->groups[$name]);
    }

    public function toString() {
        $form = new Node('form', null, $this->getAttrs());
        if ($this->count()) {
            foreach ($this->getElements() as $element) {
                $form->appendContent($element->toString());
            }
        }
        return $form->toString();
    }
    
    public function openTag() {
        $form = new Node('form', false, $this->getAttrs());
        return $form->openTag();
    }
    
    public function closeTag() {
        return '</form>';
    }

    abstract public function createElements(array $elements);
}
