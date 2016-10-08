<?php

namespace Nth\File;

use Nth\File\Image;
use Nth\Html\Node;

class Icon extends Image {

    public function toHtml($size = 16) {
        return $this->getNode($size)->toString();
    }
    
    public function getNode($size = 16) {
        return new Node('img', false, [
            'alt' => 'image icon',
            'src' => $this->getDataSource(),
            'height' => $size
        ]);
    }

}
