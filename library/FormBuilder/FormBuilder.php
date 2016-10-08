<?php

namespace Nth\FormBuilder;

class FormBuilder {
    
    public static function decodeXmlData($string) {
        if (empty($string) || !is_string($string)) {
            return $string;
        }
        return json_decode(urldecode($string));
    }
    
    public static function encodeXmlData($data) {
        return json_encode($data);
    }
    
}