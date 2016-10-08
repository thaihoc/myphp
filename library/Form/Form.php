<?php

namespace Nth\Form;

use Nth\Form\AbstractForm;
use Nth\Form\FormInterface;
use Nth\Form\Group\Group;

class Form extends AbstractForm implements FormInterface {
    
    public function __construct(array $elements = array(), $data = array(), array $attrs = array()) {
        parent::__construct($this->initElementGroups($elements), $data, self::$defaultAttrs + $attrs);
    }

    public function createElements(array $elements) {
        if (count($elements) === 0) {
            return [];
        }
        foreach ($elements as &$element) {
            if (empty($element['groups'])) {
                continue;
            }
            $groups = [];
            foreach ((array) $element['groups'] as $name) {
                if (!$this->existsGroup($name)) {
                    $this->addGroup(new Group($name));
                }
                array_push($groups, $this->getGroup($name));
            }
            $element['groups'] = $groups;
        }
        return $elements;
    }

}
