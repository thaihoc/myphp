<?php

namespace Nth\File;

use Nth\File\FileInterface;

abstract class AbstractFile {

    public static $directorySeparator = '/';
    protected $path;

    public function __construct($path) {
        $this->setPath($path);
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $this->cleanPath($path);
    }
    
    protected function cleanPath($path) {
        $replace = self::$directorySeparator;
        $find = $replace === '/' ? '\\' : '/';
        return str_replace($find, $replace, $path);
    }
    
    public function getRealPath(){
        return realpath($this->path);
    }

    public static function removeInvalidChars($name) {
        return preg_replace(FileInterface::REMOVE_INVALID_CHARACTER_REGEX, '', $name);
    }
    
    public static function setDirectorySeparator($directorySeparator) {
        self::$directorySeparator = $directorySeparator;
    }
    
    public static function getDirectorySeparator() {
        if (empty(self::$directorySeparator)) {
            return DIRECTORY_SEPARATOR;
        }
        return self::$directorySeparator;
    }

}
