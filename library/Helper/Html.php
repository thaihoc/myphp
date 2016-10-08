<?php

namespace Nth\Helper;

use Nth\Html\Node;
use Nth\FormBuilder\Element\Select;

class Html {

    public static function getOptions($data, $name = NULL, $value = NULL, $selected = NULL, $emptyOption = NULL) {
        $select = new Select('FOR_GETTING_OPTIONS', [], [
            'bindOptions' => [
                'name' => $name,
                'value' => $value,
                'data' => $data,
                'selected' => $selected,
                'emptyOption' => $emptyOption === true ? '-- Chưa chọn --' : $emptyOption
            ]
        ]);
        return $select->getSelectItems();
    }

    public static function hidden($name, $value = null) {
        $attrs = array(
            'type' => 'hidden',
        );
        if (is_array($name)) {
            $attrs = array_merge($attrs, $name);
        } else {
            $attrs['id'] = $name;
            $attrs['name'] = $name;
        }
        if (is_array($value)) {
            $attrs = array_merge($attrs, $value);
        } else {
            $attrs['value'] = $value;
        }
        $hidden = new Node('input', false, $attrs);
        return $hidden->toString();
    }

}
