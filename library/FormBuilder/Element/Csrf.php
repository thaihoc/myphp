<?php

namespace Nth\FormBuilder\Element;

use Nth\FormBuilder\Element\AbstractElement;
use Nth\FormBuilder\Element\ElementInterface;
use Zend\Validator\Csrf as CsrfValidator;

class Csrf extends AbstractElement implements ElementInterface {
    
    const DEFAULT_NAME = '_FB_CSRF_Element';
    
    private $csrfValidator;
    
    public function __construct($name = self::DEFAULT_NAME, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::CSRF, $name, $attrs, $options, $order, $groups);
        $this->csrfValidator = new CsrfValidator($name);
        $this->setAttr('value', $this->getCsrfValidator()->getHash());
    }

    public function getControlNodeName() {
        return 'input';
    }

    public function toString() {
        $this->setAttr('type', 'hidden');
        $this->setOption('controlOnly', true);
        $control = $this->getComponents()->getControl()->getNode();
        $control->setContent(false);
        return parent::toString();
    }

    public function getValue() {
        return $this->getAttr('value');
    }

    public function setValue($value) {
        $this->setAttr('value', $value);
        return $this;
    }
    
    public function getCsrfValidator() {
        return $this->csrfValidator;
    }

    public function setCsrfValidator(CsrfValidator $csrfValidator) {
        $this->csrfValidator = $csrfValidator;
    }


}
