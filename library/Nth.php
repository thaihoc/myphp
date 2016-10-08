<?php

namespace Nth;

use Nth\Html\Node;

class Nth {

    public static function nvl($text, $def = NULL) {
        if (empty($text)) {
            return $def;
        }
        return $text;
    }

    public static function dump($var) {
        ob_start();
        var_dump($var);
        echo (new Node('pre', ob_get_clean()))->toString();
    }
    
    public static function getLibDir() {
        return __DIR__;
    }

}
