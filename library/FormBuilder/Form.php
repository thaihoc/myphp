<?php

namespace Nth\FormBuilder;

use Nth\FormBuilder\Element\Element;
use Nth\FormBuilder\Group\Group;
use Nth\FormBuilder\AbstractForm;
use Nth\FormBuilder\FormInterface;

class Form extends AbstractForm implements FormInterface {

    public function __construct(array $elements = array(), $data = array(), array $attrs = array()) {
        parent::__construct($this->createElements($elements), $data, self::$defaultAttrs + $attrs);
        $this->bindValues();
    }

    public function createElements(array $elements) {
        if (count($elements) === 0) {
            return [];
        }
        $i = 0;
        foreach ($elements as $key => &$element) {
            $groups = (array) $element['groups'];
            unset($element['groups']);
            $element['name'] = isset($element['name']) ? $element['name'] : $key;
            $element['order'] = isset($element['order']) ? $element['order'] : ++$i;
            $element = Element::fromArray($element);
            $this->bindElementToGroups($element, $groups);
        }
        return $elements;
    }

    private function bindElementToGroups(&$element, array $groups) {
        if (empty($groups)) {
            return $this;
        }
        foreach ($groups as $name) {
            if (!$this->existsGroup($name)) {
                $this->addGroup(new Group($name));
            }
            $group = $this->getGroup($name);
            $group->add($element->joinGroup($group));
        }
        return $this;
    }
    
    public static function decodeXmlData($string) {
        if (!$string) {
            return $string;
        }
        return json_decode(urldecode($string));
    }

}
