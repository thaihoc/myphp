<?php

namespace Nth\File;

use Exception;
use Nth\File\AbstractFile;
use Nth\File\FileInterface;
use Nth\File\File;
use Nth\Platform\OS;

class Folder extends AbstractFile implements FileInterface {

    public function exists() {
        return is_dir($this->path);
    }

    public function create($mode = 0755, $recursive = false) {
        return mkdir($this->path, $mode, $recursive);
    }

    /**
     * remove folder
     * @param: $rmsfaf (remove all subfiles and subfolders its contain)
     */

    public function remove($rmsfaf = false) {
        if (!$this->exists()) {
            return false;
        }
        $directory = $this->getRealPath();
        $files = $this->getContainsFile();
        if (!empty($files)) {
            if (!$rmsfaf) {
                throw new Exception("Can not remove $directory folder because it's not empty");
            }
            if (!$this->makeEmpty()) {
                return false;
            }
        }
        return rmdir($directory);
    }

    /**
     * remove all subfiles and subfolders recursive
     * @name makeEmpty
     */

    public function makeEmpty() {
        $files = $this->getContainsFile();
        if (empty($files)) {
            return true;
        }
        $result = true;
        $directory = $this->getPath();
        foreach ($files as $file) {
            $path = $directory . self::getDirectorySeparator() . $file;
            if (is_dir($path)) {
                $folder = new Folder($path);
                $result = $folder->remove(true);
            } else {
                $file = new File($path);
                $result = $file->remove();
            }
            if (!$result) {
                break;
            }
        }
        return $result;
    }

    public function getContainsFile() {
        return array_diff(scandir($this->getPath()), array('.', '..'));
    }
    
    public function containsFile() {
        return !empty($this->getContainsFile());
    }

    public function copyTo($path) {
        if (!$this->containsFile()) {
            return true;
        }
        $result = true;
        foreach ($this->getContainsFile() as $file) {
            $copyPath = $this->getPath() . self::getDirectorySeparator() . $file;
            if (is_dir($copyPath)) {
                $folder = new Folder($copyPath);
                $result = $folder->copyTo($path . self::getDirectorySeparator() . $file);
            } else {
                $file = new File($copyPath);
                $result = $file->copyTo($path);
            }
            if (!$result) {
                break;
            }
        }
        return $result;
    }
    
    public function moveTo($path) {
        if ($this->copyTo($path)) {
            return $this->remove(true);
        }
        return false;
    }

    public function getBasename() {
        $array = explode(self::getDirectorySeparator(), $this->path);
        return empty($array) ? null : $array[count($array) - 1];
    }

    public function getDirname() {
        $array = explode(self::getDirectorySeparator(), $this->path);
        if (empty($array)) {
            return null;
        }
        unset($array[count($array) - 1]);
        return implode(self::getDirectorySeparator(), $array);
    }

    public function getExtension() {
        return null;
    }

    public function getFilename() {
        $array = explode(self::getDirectorySeparator(), $this->path);
        return empty($array) ? null : $array[count($array) - 1];
    }

    public function getSize($platform = 1) {
        if (OS::WINDOW === $platform) {
            $obj = new COM('scripting.filesystemobject');
            if (is_object($obj)) {
                $ref = $obj->getfolder($this->path);
                return $ref->size;
            }
            return 0;
        } elseif (OS::LINUX === $platform) {
            $f = $this->getPath();
            $io = popen('/usr/bin/du -sk ' . $f, 'r');
            $size = fgets($io, 4096);
            pclose($io);
            return substr($size, 0, strpos($size, "\t"));
        } else {
            throw new Exception('Invalid platform ' . $platform);
        }
    }

    public function rename($name) {
        $newpath = $this->getDirname() . self::getDirectorySeparator() . $name;
        $folder = new Folder($newpath);
        if (!$folder->exists()) {
            $folder->create(0755, true);
        }
        if ($this->moveTo($newpath)) {
            $this->setPath($newpath);
            return true;
        }
        return false;
    }

    public function getMimeType() {
        return null;
    }

    public function toCurlFile() {
        return null;
    }

}
