<?php

namespace Nth\Helper;

use Nth\Nth;
use Nth\File\File;
use Nth\File\Icon as IconFile;

class Icon {

    public static function getPathMap($size = 16) {
        $iconsDir = Nth::getLibDir() . '/../images/icons';
        return array(
            '?' => "$iconsDir/blank_$size.png",
            'doc' => "$iconsDir/word_$size.gif",
            'docx' => "$iconsDir/word_$size.gif",
            'xls' => "$iconsDir/excel_$size.gif",
            'xlsx' => "$iconsDir/excel_$size.gif",
            'ppt' => "$iconsDir/powerpoint_$size.png",
            'pptx' => "$iconsDir/powerpoint_$size.png",
            'pdf' => "$iconsDir/pdf_$size.png",
            'dxf' => "$iconsDir/dxf_$size.png",
            'rar' => "$iconsDir/rar_$size.png",
            'zip' => "$iconsDir/zip_$size.png",
            'jpg' => "$iconsDir/jpg_$size.png",
            'jpeg' => "$iconsDir/jpg_$size.png",
            'png' => "$iconsDir/png_$size.png",
            'gif' => "$iconsDir/gif_$size.gif",
        );
    }

    public static function getImageHtml($path, $size = 16) {
        return self::getObject($path, $size)->toHtml($size);
    }
    
    public static function getImageNode($path, $size = 16) {
        return self::getObject($path, $size)->getNode($size);
    }
    
    public static function getObject($path, $size = 16) {
        $file = new File($path);
        $pathMap = self::getPathMap($size);
        $path = $pathMap['?'];
        $ext = strtolower($file->getExtension());
        if (isset($pathMap[$ext])) {
            $path = $pathMap[$ext];
        }
        return new IconFile($path);
    }

}
