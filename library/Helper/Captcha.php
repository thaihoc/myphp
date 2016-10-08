<?php

namespace Nth\helper;

use Zend\Session\Container;

class Captcha {

    const SESSION_PREFIX = 'IGATE_CAPTCHA_';

    private $container;
    private $name;

    public function __construct($name) {
        $this->name = $name;
        $this->container = new Container(ZF2_DEFAULT_SESSION_CONTAINER);
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function create() {
        $string = '';
        for ($i = 0; $i < 5; $i++) {
            $string .= chr(rand(97, 122));
        }
        $this->container->offsetSet(self::SESSION_PREFIX . $this->name, $string);

        $dir = CFG_SERVER_ROOT . 'public/fonts/';
        $image = imagecreatetruecolor(165, 50); //custom image size
        $font = "Walkway Black RevOblique.ttf"; // custom font style
        $color = imagecolorallocate($image, 113, 193, 217); // custom color
        $white = imagecolorallocate($image, 255, 255, 255); // custom background color
        imagefilledrectangle($image, 0, 0, 399, 99, $white);
        imagettftext($image, 30, 0, 10, 40, $color, $dir . $font, $this->container->offsetGet(self::SESSION_PREFIX . $this->name));
        header("Content-type: image/png");
        return imagepng($image);
    }

    public function check($code) {
        $captcha = $this->container->offsetGet(self::SESSION_PREFIX . $this->name);
        return $captcha == $code ? 1 : 0;
    }

}
