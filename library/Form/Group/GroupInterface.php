<?php

namespace Nth\Form\Group;

use Nth\Html\AttributesInterface;

interface GroupInterface extends AttributesInterface {

    const ESC = 'ESC';
    const DESC = 'DESC';

    public function get($name);

    public function add($element);
    
    public function mergeWith();
    
    public function mergeElements(array $elements);

    public function getName();

    public function setName($name);

    public function setElements(array $elements);

    public function getElements();

    public function getElement($name);

    public function appendElement($element);

    public function prependElement($element);

    public function removeElement($name);

    public function existsElement($name);

    public function count();

    public function sort($mode = 'ESC');

    public function getDefaultConfig();

    public function bindValues();

    public function setData($data);

    public function getData($offset = null, $def = null);

    public function toString();
}
