<?php

namespace Nth\Http\PhpEnvironment;

use Zend\Stdlib\ArrayObject;
use Zend\Http\PhpEnvironment\Request;
use Nth\Http\PhpEnvironment\File;

class Files extends ArrayObject {

    private $name;

    public function __construct($name) {
        parent::__construct([], ArrayObject::ARRAY_AS_PROPS);
        $this->setName($name);
    }

    public static function remap($files) {
        if (isset($files['name'])) {
            $files = [$files];
        }
        foreach ($files as &$file) {
            if (!$file instanceof File) {
                $file = new File($file);
            }
        }
        return $files;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        if (!is_null($name)) {
            $request = new Request();
            $this->name = $name;
            $this->setFiles((array) $request->getFiles($name));
        }
        return $this;
    }

    public function getFiles() {
        return $this->getArrayCopy();
    }

    public function setFiles(array $files) {
        $this->storage = self::remap($files);
        return $this;
    }

    public function count() {
        return count($this->getArrayCopy());
    }

    public function getFile($index) {
        return $this->offsetGet($index);
    }

    public function setFile($index, File $file) {
        $this->offsetSet($index, $file);
        return $this;
    }

}
