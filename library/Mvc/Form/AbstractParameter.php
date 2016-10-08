<?php

namespace Nth\Mvc\Form;

use Nth\Mvc\Controller\ContextController;
use Nth\Db\Oracle\Parameters;

abstract class AbstractParameter extends ContextController {

    protected function &getResult($result, $name, $prefix = false){
        $parameter = new Parameters($result);
        if ($prefix) {
            $parameter->addPrefix();
        }
        $this->{$name} = $parameter->toArrayObjectValue();
        return $this->{$name};
    }
    
    public function existsParameter($name){
        return isset($this->{$name});
    }
    
    public function &getParameter($name){
        return $this->{$name};
    }

}
