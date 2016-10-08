<?php

namespace Nth\Form;

use Nth\Bootstrap\Alert\Alert;
use Nth\Html\Node;
use Zend\Stdlib\ArrayObject;

class ActionResult extends ArrayObject{

    private $controller;
    private $error;
    private $message;
    private $level;

    public function __construct($controller = null, $result = array()) {
        if (is_array($controller)) {
            $result = $controller;
        } else {
            $this->setController($controller);
        }
        $this->level = Alert::SUCCESS;
        parent::__construct($result, ArrayObject::ARRAY_AS_PROPS);
        $this->analyze();
    }
    
    private function analyze(){
        if($this->P_ERROR){
            $this->setError($this->P_ERROR);
        }
        return $this;
    }
    
    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message, $level = null) {
        $this->message = $message;
        if (!is_null($level)) {
            $this->level = $level;
        }
        return $this;
    }
    
    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }

    public function getController() {
        return $this->controller;
    }

    public function setController($controller) {
        $this->controller = $controller;
    }

    public function offsetGetLink($name, $def = null) {
        $value = $this->offsetGet($name, $def);
        if (!empty($value)) {
            $a = new Node('a', $value, [
                'target' => '_blank',
                'href' => $value
            ]);
            return $a->toString();
        }
        return $value;
    }
    
    public function setResult($result){
        if ($result instanceof ArrayObject) {
            $result = $result->getArrayCopy();
        }
        $this->storage = $result;
        return $this->analyze();
    }
    
    public function getResult(){
        return $this->getArrayCopy();
    }

    public function getError() {
        return $this->error;
    }

    public function setError($error) {
        $this->error = $error;
        return $this;
    }

    public function success() {
        return (int) $this->P_KQ === 1;
    }

    public function hasError() {
        return !empty($this->getError());
    }

    public function redirectX($route, $url = null) {
        if ($url) {
            $this->getController()->redirect()->toUrl($url);
        } else {
            $this->getController()->redirect()->toRoute($route);
        }
    }

    public function getErrorHtml() {
        if (!$this->hasError()) {
            return null;
        }
        $title = new Node('strong', 'Lỗi!');
        $message = new Node('span', $this->getError());
        $alert = new Alert($title, $message, Alert::DANGER);
        $wrapper = new Node('div', $alert->toHtml(), ['style' => ['margin-top' => '10px']]);
        return $wrapper->toString();
    }
    
    public function getMessageHtml(){
        if ($this->hasError()) {
            return $this->getErrorHtml();
        }
        $message = $this->getMessage();
        if (empty($message)) {
            return null;
        }
        $alert = new Alert(new Node('strong', 'Thông báo!'), new Node('span', $message), $this->level);
        $wrapper = new Node('div', $alert->toHtml(), ['style' => ['margin-top' => '10px']]);
        return $wrapper->toString();
    }

}
