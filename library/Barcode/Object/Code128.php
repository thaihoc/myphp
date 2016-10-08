<?php

namespace Nth\Barcode\Object;

use Zend\Barcode\Object\Code128 as ZCode128;

class Code128 extends ZCode128 {

    private $textToDisplay;

    public function setTextToDisplay($text) {
        $this->textToDisplay = $text;
        return $this;
    }

    public function getTextToDisplay() {
        return $this->textToDisplay ? : $this->getText();
    }

}
