<?php

namespace Nth\File\Upload;

use Nth\File\FileInterface;
use Nth\File\File;
use Nth\File\Folder;
use Nth\File\Upload\Uploader;
use Zend\Http\PhpEnvironment\Request;

class ResultSet {

    const STRING_PATH = 1;
    const LIST_PATH = 2;
    const STRING_NAME = 3;
    const LIST_NAME = 4;
    const DELIMETER = ':';

    private $uploader;
    private $type;

    public function __construct(Uploader $uploader, $type = 1) {
        $this->uploader = $uploader;
        $this->type = $type;
    }

    public function getUploader() {
        return $this->uploader;
    }

    public function setUploader(Uploader $uploader) {
        $this->uploader = $uploader;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getResult() {
        if (self::STRING_PATH === $this->type) {
            return implode(self::DELIMETER, $this->getAllPaths());
        } elseif (self::LIST_PATH === $this->type) {
            return $this->getAllPaths();
        } elseif (self::STRING_NAME === $this->type) {
            return implode(self::DELIMETER, $this->getAllNames());
        } elseif (self::LIST_NAME === $this->type) {
            return $this->getAllNames();
        } else {
            return null;
        }
    }

    public function getAllPaths() {
        $paths = $this->getRemainPaths();
        $directory = $this->getUploader()->createDirectory();
        $files = $this->getUploader()->getFiles();
        for ($i = 0; $i < $files->count(); $i++) {
            $file = $files->getFile($i);
            if (!$file->exists()) {
                continue;
            }
            array_push($paths, $directory . File::getDirectorySeparator() . $file->getBasename());
        }
        return $paths;
    }

    public function getAllNames() {
        $names = $this->getRemainNames();
        $files = $this->getUploader()->getFiles();
        for ($i = 0; $i < $files->count(); $i++) {
            $file = $files->getFile($i);
            if (!$file->exists()) {
                continue;
            }
            array_push($names, $this->getSubDirectory($file) . $file->getBasename());
        }
        return $names;
    }

    public function getRemainNames() {
        $request = new Request();
        $files = $request->getPost($this->getUploader()->getRmafcnPrefix() . $this->getUploader()->getName());
        if (empty($files)) {
            return [];
        }
        $names = [];
        for($i = 0; $i < count($files); $i++){
            $file = new File($files[$i]);
            if (!$file->exists()) {
                continue;
            }
            array_push($names, $this->getSubDirectory($file) . $file->getBasename());
        }
        return $names;
    }
    
    public function getSubDirectory(FileInterface $file) {
        if ($this->getUploader()->hasSubDirectory()) {
            $folder = new Folder($file instanceof File ? $file->getDirname() : $this->getUploader()->createDirectory());
            return $folder->getBasename() . File::getDirectorySeparator();
        }
        return;
    }
    
    public function getRemainPaths(){
        $request = new Request();
        $files = $request->getPost($this->getUploader()->getRmafcn());
        if (empty($files)) {
            return [];
        }
        return explode(self::DELIMETER, $files);
    }

}
