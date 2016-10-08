<?php

namespace Nth\File\Upload;

use Nth\File\Validator;
use Nth\File\File;
use Nth\File\Folder;
use Nth\File\Upload\ResultSet;
use Nth\Http\PhpEnvironment\Files;
use Zend\Http\PhpEnvironment\Request;

class Uploader {

    const DEFAULT_MODE = 0;
    const AUTO_RENAME_FILE = 1;
    const AUTO_CREATE_SUB_FOLDER = 2;
    const NORMAL_MODE = 3;
    const REMAINING_FILES_CONTROL_NAME_PREFIX = '_remain_files_';
    const REMOVING_FILES_CONTROL_NAME_PREFIX = '_remove_files_';

    private $directory;
    private $permission;
    private $name;
    private $mode;
    private $validator;
    private $files;
    private $message;
    private $resultSet;
    private $subDirectory;
    private $rmafcnPrefix;
    private $rmofcnPrefix;
    private $rmafcn;
    private $rmofcn;
    private $directorySeparator;

    public function __construct($directory = null, $name = null, $mode = 0, $permission = 0755) {
        $this->directory = $directory;
        $this->name = $name;
        $this->mode = $mode;
        $this->permission = $permission;
        $this->resultSet = new ResultSet($this);
        $this->validator = new Validator(null);
        $this->files = new Files($name);
        $this->rmafcnPrefix = self::REMAINING_FILES_CONTROL_NAME_PREFIX;
        $this->rmofcnPrefix = self::REMOVING_FILES_CONTROL_NAME_PREFIX;
        $this->rmafcn = null;
        $this->rmofcn = null;
        $this->directorySeparator = '/';
    }
    
    public function getDirectorySeparator() {
        return $this->directorySeparator;
    }

    public function setDirectorySeparator($directorySeparator) {
        $this->directorySeparator = $directorySeparator;
        return $this;
    }
    
    public function getRmafcn() {
        if (is_null($this->rmafcn)) {
            return $this->getRmafcnPrefix() . $this->getName();
        }
        return $this->getRmafcnPrefix() . $this->rmafcn;
    }

    public function getRmofcn() {
        if (is_null($this->rmofcn)) {
            return $this->getRmofcnPrefix() . $this->getName();
        }
        return $this->getRmofcnPrefix() . $this->rmofcn;
    }

    public function setRmafcn($rmafcn) {
        $this->rmafcn = $rmafcn;
        return $this;
    }

    public function setRmofcn($rmofcn) {
        $this->rmofcn = $rmofcn;
        return $this;
    }
    
    public function getRmafcnPrefix() {
        return $this->rmafcnPrefix;
    }

    public function getRmofcnPrefix() {
        return $this->rmofcnPrefix;
    }

    public function setRmafcnPrefix($rmafcnPrefix) {
        $this->rmafcnPrefix = $rmafcnPrefix;
        return $this;
    }

    public function setRmofcnPrefix($rmofcnPrefix) {
        $this->rmofcnPrefix = $rmofcnPrefix;
        return $this;
    }
    
    public function getDirectory() {
        return $this->directory;
    }

    public function getMode() {
        return $this->mode;
    }

    public function setDirectory($directory) {
        $this->directory = $directory;
        return $this;
    }

    public function setMode($mode) {
        $this->mode = $mode;
        return $this;
    }

    public function getMessage() {
        return $this->message;
    }

    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }
    
    public function getPermission() {
        return $this->permission;
    }

    public function getName() {
        return $this->name;
    }

    public function setPermission($permission) {
        $this->permission = $permission;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        $this->getFiles()->setName($name);
        return $this;
    }

    public function &getFiles() {
        return $this->files;
    }

    public function setFiles($files) {
        $this->files = $files;
        return $this;
    }

    public function upload() {
        if ($this->getMessage()) {
            return false;
        }
        $directory = $this->createDirectory();
        $files = $this->getFiles();
        for ($i = 0; $i < $files->count(); $i++) {
            $file = $files->getFile($i);
            if (!$file->exists()) {
                continue;
            }
            if (!$this->validateFile($file)) {
                return false;
            }
            if (!$file->moveTo($directory . File::getDirectorySeparator() . $file->getBasename())) {
                $this->setMessage('Không thể tải lên tệp tin ' . $file->getBasename());
                $this->rollback();
                return false;
            }
        }
        $this->removeTrashFiles();
        return $this->getResultSet()->getResult();
    }

    public function rollback() {
        $directory = $this->createDirectory();
        $files = $this->getFiles();
        for ($i = 0; $i < $files->count(); $i++) {
            $file = new File($directory . File::getDirectorySeparator() . $file->getFiles($i)->getBasename());
            if ($file->exists()) {
                $file->remove();
            }
        }
    }

    protected function validateFile(&$file) {
        $this->validator->setFile($file);
        if (!$this->validator->validate()) {
            $message = $this->validator->getErrorMessage();
            $this->setMessage($message);
            return false;
        }
        $file->stripUnicode();
        $suffix = $this->getFilenameSuffix();
        if (!empty($suffix)) {
            $filename = $file->getFilename();
            $file->rename($filename . '_' . $suffix);
        }
        return true;
    }

    public function createDirectory() {
        $subDirectory = $this->getSubDirectory();
        $directory = empty($subDirectory) ? $this->directory : $this->directory . File::getDirectorySeparator() . $subDirectory;
        $folder = new Folder($directory);
        if(!$folder->exists()){
            $folder->create($this->permission, true);
        }
        return $folder->getPath();
    }

    protected function getSubDirectory() {
        if ($this->hasSubDirectory()) {
            return $this->subDirectory ? : $this->subDirectory = date('Y_m');
        }
        return null;
    }
    
    public function hasSubDirectory() {
        return in_array($this->mode, [self::DEFAULT_MODE, self::AUTO_CREATE_SUB_FOLDER]);
    }

    protected function getFilenameSuffix() {
        if ($this->isRandomName()) {
            return time();
        }
        return null;
    }
    
    public function isRandomName() {
        return in_array($this->mode, [self::DEFAULT_MODE, self::AUTO_RENAME_FILE]);
    }

    public function getResultSet() {
        return $this->resultSet;
    }

    public function setResultSet($resultSet) {
        $this->resultSet = $resultSet;
        return $this;
    }
    
    public function getRemoveFiles() {
        $request = new Request();
        return $request->getPost($this->getRmofcn());
    }

    public function removeTrashFiles() {
        $files = $this->getRemoveFiles();
        if (empty($files)) {
            return $this;
        }
        $array = explode(ResultSet::DELIMETER, $files);
        for($i = 0; $i < count($array); $i++){
            $file = new File($array[$i]);
            if ($file->exists()) {
                $file->remove();
            }
        }
        return $this;
    }
    
    public function getRemainFiles() {
        $request = new Request();
        return $request->getPost($this->getRmafcn());
    }
    
    public function removeRemainFiles() {
        $files = $this->getRemainFiles();
        if (empty($files)) {
            return $this;
        }
        $array = explode(ResultSet::DELIMETER, $files);
        for($i = 0; $i < count($array); $i++){
            $file = new File($array[$i]);
            if ($file->exists()) {
                $file->remove();
            }
        }
        return $this;
    }
    
    public function removeUploadedFiles() {
        $this->removeTrashFiles();
        $this->removeRemainFiles();
        return $this;
    }
    
    public function getValidator() {
        return $this->validator;
    }

    public function setValidator($validator) {
        $this->validator = $validator;
    }

}
