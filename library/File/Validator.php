<?php

namespace Nth\File;

class Validator {

    const FILE_SIZE_TOO_LARGE = 1;
    const FILE_SIZE_TOO_SMALL = 2;
    const FILE_EXTENSION_INVALID = 3;

    /*
     * private $maxSize set in bytes
     */

    private $maxSize;
    
    /*
     * private $minSize set in bytes
     */
    
    private $minSize;
    
    /*
     * private $extensions is a regular expression
     */
    
    private $extensions;
    
    
    /*
     * private $file is object instanceof \Nth\Http\PhpEnvironment\File
     */
    
    private $file;
    
    /*
     * private $errorCode error occurred during validation
     */
    
    private $errorCode;

    public function __construct($file, $extensions = null, $maxSize = null, $minSize = 0) {
        if (is_null($maxSize)) {
            $maxSize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
        }
        $this->file = $file;
        $this->extensions = $extensions;
        $this->maxSize = $maxSize;
        $this->minSize = $minSize;
    }

    public function getFile() {
        return $this->file;
    }

    public function setFile($file) {
        $this->file = $file;
    }

    public function getErrorCode() {
        return $this->errorCode;
    }

    public function setErrorCode($errorCode) {
        $this->errorCode = $errorCode;
    }

    public function getErrorMessage() {
        $errorCode = $this->getErrorCode();
        $basename = $this->getFile()->getBasename();
        switch ($errorCode) {
            case self::FILE_EXTENSION_INVALID:
                return "Phần mở rộng của tệp tin $basename không được phép";
            case self::FILE_SIZE_TOO_LARGE:
                return "Kích thước tệp tin $basename quá lớn";
            case self::FILE_SIZE_TOO_SMALL:
                return "Kích thước tệp tin $basename quá nhỏ";
            default:
                return null;
        }
    }

    public function getMaxSize() {
        return $this->maxSize;
    }

    public function getExtensions() {
        return $this->extensions;
    }

    public function getMinSize() {
        return $this->minSize;
    }

    public function setMinSize($minSize) {
        $this->minSize = $minSize;
    }

    public function setMaxSize($maxSize) {
        $this->maxSize = $maxSize;
    }

    public function setExtensions($extensions) {
        $this->extensions = $extensions;
    }

    public function validate() {
        $file = $this->getFile();
        $extensions = $this->getExtensions();
        if (!empty($extensions)) {
            preg_match($extensions, $file->getExtension(), $matches);
            if (empty($matches)) {
                $this->setErrorCode(self::FILE_EXTENSION_INVALID);
                return false;
            }
        }
        if ($this->getMaxSize() < $file->getSize()) {
            $this->setErrorCode(self::FILE_SIZE_TOO_LARGE);
            return false;
        }
        if ($this->getMinSize() > $file->getSize()) {
            $this->setErrorCode(self::FILE_SIZE_TOO_SMALL);
            return false;
        }
        return true;
    }

}
