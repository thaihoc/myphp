<?php

namespace Nth\File;

interface FileInterface {

    const REALPATH_VALIDATION_REGEX = '/^[A-Z]{1}:\\\[\.\s\[\]%\+a-zA-Z_\\\-]+$/';
    const RELATIVE_PATH_VALIDATION_REGEX = '/^[\.\s\[\]%\+a-zA-Z_\\\-]+$/';
    const DRIVE_VALIDATION_REGEX = '/^[A-Z]{1}:\\\/';
    const REMOVE_INVALID_CHARACTER_REGEX = '/[<>\*\?\|\\\"\/:\\\]/';

    public function remove();

    public function exists();

    public function getBasename();

    public function getExtension();

    public function getDirname();

    public function getFilename();

    public function getSize();
    
    public function getMimeType();

    public function rename($name);
    
    public function copyTo($path);
    
    public function moveTo($path);
    
    public function toCurlFile();
}
