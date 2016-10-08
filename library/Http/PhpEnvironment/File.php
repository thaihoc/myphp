<?php

namespace Nth\Http\PhpEnvironment;

use CURLFile;
use Nth\File\FileInterface;
use Nth\File\AbstractFile;
use Nth\Helper\String;

class File implements FileInterface {

    private $info;

    public function __construct(array $info) {
        $this->info = $info;
    }

    public function getInfo($option = null, $def = null) {
        if (is_null($option)) {
            return $this->info;
        }
        return isset($this->info[$option]) ? $this->info[$option] : $def;
    }

    public function setInfo(array $info) {
        $this->info = $info;
    }

    public function exists() {
        return (int) $this->getSize() > 0;
    }

    public function getBasename() {
        return $this->getInfo('name');
    }

    public function getDirname() {
        return $this->getInfo('dirname');
    }

    public function getExtension() {
        $name = $this->getInfo('name');
        $array = explode('.', $name);
        return empty($array) ? null : $array[count($array) - 1];
    }

    public function getFilename() {
        $name = $this->getInfo('name');
        $array = explode('.', $name);
        if (empty($array)) {
            return null;
        }
        unset($array[count($array) - 1]);
        return implode('.', $array);
    }

    public function getSize() {
        return $this->getInfo('size', 0);
    }
    
    public function getError(){
        return $this->getInfo('error');
    }

    public function remove() {
        return $this->setInfo([]);
    }

    public function rename($name) {
        $this->info['name'] = $name . '.' . $this->getExtension();
    }
    
    public function setDirname($dirname){
        $this->info['dirname'] = $dirname;
    }
    
    public function getTempname(){
        return $this->getInfo('tmp_name');
    }
    
    public function removeInvalidChars(){
        $this->info['name'] = AbstractFile::removeInvalidChars($this->info['name']);
        return $this;
    }
    
    public function stripUnicode() {
        $filename = $this->getFilename();
        $extension = $this->getExtension();
        $filename = String::rmMarks($filename);
        $filename = String::cleanSpecialChars($filename);
        $this->info['name'] = $filename . '.' . $extension;
        return $this;
    }

    public function copyTo($path) {
        return move_uploaded_file($this->getTempname(), $path);
    }

    public function moveTo($path) {
        return move_uploaded_file($this->getTempname(), $path);
    }

    public function getMimeType() {
        return $this->getInfo('type');
    }

    public function toCurlFile() {
        if ($this->exists()) {
            return new CURLFile($this->getTempname(), $this->getMimeType(), $this->getBasename());
        }
        return null;
    }

}
