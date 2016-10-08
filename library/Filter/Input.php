<?php

namespace Nth\Filter;

class Input {
    
    public function filter($value) {
        if (is_string($value)) {
            $value = stripslashes($value);
            $value = str_replace("&", '&amp;', $value);
            $value = str_replace('<', '&lt;', $value);
            $value = str_replace('>', '&gt;', $value);
            $value = str_replace('"', '&#34;', $value);
            $value = str_replace("'", '&#39;', $value);
        }
        return $value;
    }
    
}