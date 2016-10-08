<?php

namespace Nth\Bootstrap\Alert;

use Nth\Html\Node;

class Alert {

    const SUCCESS = 'alert-success';
    const INFO = 'alert-info';
    const WARNING = 'alert-warning';
    const DANGER = 'alert-danger';

    private $title;
    private $message;
    private $level;
    private $dismissible;

    public function __construct($title = null, $message = null, $level = self::SUCCESS, $dismissible = true) {
        $this->title = $title;
        $this->message = $message;
        $this->level = $level;
        $this->dismissible = $dismissible;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getLevel() {
        return $this->level;
    }    
    
    public function getLevelClass(){
        return $this->level;
    }

    public function getDismissible() {
        return $this->dismissible;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }

    public function setDismissible($dismissible) {
        $this->dismissible = $dismissible;
        return $this;
    }

    public function toHtml() {
        $title = $this->getTitle();
        $message = $this->getMessage();
        if(!$title instanceof Node){
            $title = new Node('span', $title);
        }
        if(!$message instanceof Node){
            $message = new Node('span', $message);
        }
        $div = new Node('div', null, [
            'role' => 'alert',
            'class' => 'alert ' . $this->getLevelClass(),
        ]);
        if($this->getDismissible()){
            $div->setAttr('class', $div->getAttr('class') . ' alert-dismissible');
            $div->appendContent('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        }
        $div->appendContent(sprintf('%s %s', $title->toString(), $message->toString()));
        return $div->toString();
    }
    
    public function toString() {
        return $this->toHtml();
    }

}
