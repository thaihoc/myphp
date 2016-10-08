<?php

namespace Nth\Http\PhpEnvironment;

use Zend\Http\PhpEnvironment\Request;
use Nth\Filter\Input as InputFilter;

class Parameters {

    private $request;
    private $filter;
    private $data;

    public function __construct() {
        $this->request = new Request();
        $this->filter = new InputFilter();
    }
    
    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getFilter() {
        return $this->filter;
    }

    public function setRequest(Request $request) {
        $this->request = $request;
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }

}
