<?php

namespace Nth\Mvc\Controller;

use Exception;

class ContextController {

    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
        $class = get_called_class();
        $methods = get_class_methods($class);
        for ($i = 0; $i < count($methods); $i++) {
            if (substr($methods[$i], 0, 2) === '__') {
                continue;
            }
            if (method_exists($this->controller, $methods[$i])) {
                $message = sprintf('The method\'s %s was existed in the %s class', $methods[$i], $class);
                throw new Exception($message);
            }
        }
    }

    public function __call($name, $arguments) {
        if (is_callable(array($this->controller, $name))) {
            return call_user_func_array(array($this->controller, $name), $arguments);
        } 
    }

    public function getController() {
        return $this->controller;
    }

}
