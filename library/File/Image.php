<?php

namespace Nth\File;

use Nth\File\File;

class Image extends File {

    public function getDataSource() {
        return sprintf('data:image/%s;base64, %s', $this->getMimeType(), base64_encode($this->getContent()));
    }

}
