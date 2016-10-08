<?php

namespace Nth\Helper;

class Client {
    
    public static function getIP() {
        if (preg_match("/^([d]{1,3}).([d]{1,3}).([d]{1,3}).([d]{1,3})$/", getenv('HTTP_X_FORWARDED_FOR'))) {
            return getenv('HTTP_X_FORWARDED_FOR');
        }
        return getenv('REMOTE_ADDR');
    }
    
}
