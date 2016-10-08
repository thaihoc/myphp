<?php

namespace Nth\FormBuilder\Element;

use Nth\Form\Element\ElementInterface as BaseElementInterface;

interface ElementInterface extends BaseElementInterface {

    public function getControlNodeName();
}
