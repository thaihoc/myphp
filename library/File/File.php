<?php

namespace Nth\File;

use CURLFile;
use Nth\File\Folder;
use Nth\File\AbstractFile;
use Nth\File\FileInterface;

class File extends AbstractFile implements FileInterface {
    
    public static $uriDownload;
    
    public static function getUriDownload() {
        if (defined('CFG_DOWNLOAD_URI')) {
            return CFG_DOWNLOAD_URI;
        }
        return self::$uriDownload;
    }
    
    public static function setUriDownload($uriDownload) {
        self::$uriDownload = $uriDownload;
    }

    public function exists() {
        return file_exists($this->path);
    }

    public function getBasename() {
        $parts = pathinfo($this->path);
        return $parts['basename'];
    }

    public function getExtension() {
        $parts = pathinfo($this->path);
        return $parts['extension'];
    }

    public function getDirname() {
        $parts = pathinfo($this->path);
        return $parts['dirname'];
    }

    public function getFilename() {
        $parts = pathinfo($this->path);
        return $parts['filename'];
    }

    public function getSize() {
        return filesize($this->path);
    }

    public function setContent($content = null, $mode = false) {
        return file_put_contents($this->path, $content, $mode ? (FILE_APPEND | LOCK_EX) : 0);
    }

    public function getContent() {
        return file_get_contents($this->path);
    }

    public function move($folder) {
        if (!$folder instanceof Folder) {
            $folder = new Folder($folder);
        }
        if (!$folder->exists()) {
            $folder->create();
        }
        $newPath = $folder->getPath() . self::getDirectorySeparator() . $this->getBasename();
        return copy($this->path, $newPath);
    }

    public function remove() {
        return unlink($this->path);
    }

    public function download() {
        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($this->path));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($this->path));
        readfile($this->path);
        return $this;
    }

    public function displaySize() {
        $bytes = $this->getSize();
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    public function rename($name) {
        return rename($this->path, $this->getDirname() . self::getDirectorySeparator() . $name);
    }

    public function copyTo($path = null, $basename = null) {
        if (is_null($path)) {
            $path = $this->getDirname();
        }
        if (is_null($basename)) {
            $basename = $this->getBasename();
        }
        $file = new File($path . self::getDirectorySeparator() . $basename);
        if ($file->exists()) {
            $basename = $this->getFilename() . ' - Copy.' . $this->getExtension();
        }
        return copy($this->path, $path . self::getDirectorySeparator() . $basename);
    }
    public function copyToFileGdoc($path = null, $basename = null) {
        if (is_null($path)) {
            $path = $this->getDirname();
        }
        if (is_null($basename)) {
            $basename = $this->getBasename();
        }
        $file = new File($path . DIRECTORY_SEPARATOR . $basename);
        if ($file->exists()) {
            $basename = $this->getFilename(). '.'.$this->getExtension().'.gdoc';
        }
        return copy($this->path, $path . DIRECTORY_SEPARATOR . $basename);
    }
   
    public function moveTo($path, $basename = null) {
        if ($this->copyTo($path, $basename)) {
            return $this->remove();
        }
        return false;
    }

    public function getMimeType() {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $this->getRealPath());
        finfo_close($finfo);
        return $mimeType;
    }

    public function getDownloadLink() {
        return rtrim(self::getUriDownload(), ' /') . '/' . $this->path;
    }

    public function toCurlFile(){
        if ($this->exists()) {
            return new CURLFile($this->getRealPath(), $this->getMimeType());
        }
        return null;
    }

}
