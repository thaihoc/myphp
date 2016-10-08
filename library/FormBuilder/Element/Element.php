<?php

namespace Nth\FormBuilder\Element;

use Exception;

class Element {

    const TEXT = 'Textbox';
    const TEXTAREA = 'Textarea';
    const CHECKBOX = 'Checkbox';
    const RADIO = 'Radio';
    const SELECT = 'Combobox';
    const DATE = 'Date';
    const FILE = 'File';
    const CAPTCHA = 'Captcha';
    const EDITOR = 'Editor';
    const TABLE = 'Table';
    const LISTS = 'List';
    const BUTTON = 'Button';
    const SUBMIT = 'Submit';
    const HIDDEN = 'Hidden';
    const CSRF = 'Csrf';

    private $element;

    public function __construct($type, $name, $attrs = [], $options = [], $order = 0, $groups = []) {
        $this->element = self::create($type, $name, $attrs, $options, $order, $groups);
    }
    
    public static function getArrayTypes() {
        return [
            self::TEXT,
            self::TEXTAREA,
            self::CHECKBOX,
            self::RADIO,
            self::SELECT,
            self::DATE,
            self::FILE,
            self::CAPTCHA,
            self::EDITOR,
            self::TABLE,
            self::BUTTON,
            self::SUBMIT,
            self::HIDDEN,
            self::CSRF,
            self::LISTS
        ];
    }

    public static function create($type, $name, $attrs = [], $options = [], $order = 0, $groups = []) {
        switch ($type) {
            case self::CAPTCHA: 
                return new Captcha($name, $attrs, $options, $order, $groups);
            case self::FILE: 
                return new File($name, $attrs, $options, $order, $groups);
            case self::SELECT: 
                return new Select($name, $attrs, $options, $order, $groups);
            case self::TEXT: 
                return new Text($name, $attrs, $options, $order, $groups);
            case self::TEXTAREA: 
                return new Textarea($name, $attrs, $options, $order, $groups);
            case self::CHECKBOX: 
                return new Checkbox($name, $attrs, $options, $order, $groups);
            case self::BUTTON: 
                return new Button($name, $attrs, $options, $order, $groups);
            case self::SUBMIT: 
                return new Submit($name, $attrs, $options, $order, $groups);
            case self::DATE: 
                return new Date($name, $attrs, $options, $order, $groups);
            case self::HIDDEN: 
                return new Hidden($name, $attrs, $options, $order, $groups);
            case self::CSRF: 
                return new Csrf($name, $attrs, $options, $order, $groups);
            default: 
                throw new Exception('Invalid element type: ' . $type);
        }
    }

    public static function fromArray(array $array) {
        if (!isset($array['name'])) {
            throw new Exception('name property is undefined for creating FormBuilder element');
        }
        $name = $array['name'];
        if (!isset($array['type']) || !in_array($array['type'], self::getArrayTypes())) {
            throw new Exception('type property is invalid for creating FormBuilder element');
        }
        $type = $array['type'];
        $attrs = isset($array['attributes']) ? (array) $array['attributes'] : [];
        $options = isset($array['options']) ? (array) $array['options'] : [];
        $order = isset($array['order']) ? (int) $array['order'] : 0;
        $groups = isset($array['groups']) ? (array) $array['groups'] : [];
        return self::create($type, $name, $attrs, $options, $order, $groups);
    }

    public static function createList(array $array) {
        $elements = [];
        if (count($array) < 1) {
            return $elements;
        }
        $i = 0;
        foreach ($array as $key => $element) {
            if ($element instanceof Element) {
                array_push($elements, $element);
            } elseif (is_array($element)) {
                $element['name'] = isset($element['name']) ? $element['name'] : $key;
                $element['order'] = isset($element['order']) ? $element['order'] : ++$i;
                array_push($elements, self::fromArray($element));
            } else {
                throw new Exception('Element must be an instance of Element or an array configruration');
            }
        }
        return $elements;
    }

    public function __call($name, $arguments) {
        if (method_exists($this->element, $name)) {
            return call_user_func_array(array($this->element, $name), $arguments);
        }
    }

}
