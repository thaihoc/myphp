<?php

namespace Nth\Mvc\Model;

use Nth\Mvc\Controller\ContextController;

abstract class AbstractModel extends ContextController {

    private $message;
    protected $parameter;

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
    }
    
    public function getParameter() {
        return $this->parameter;
    }

    public function setParameter($parameter) {
        $this->parameter = $parameter;
    }

    public function getQueryRouteOption($query) {
        return [
            'query' => $query
        ];
    }

}
