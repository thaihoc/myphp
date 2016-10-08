<?php

namespace Nth\helper;

class Number {

    public static $nts = array(
        0 => 'không', 1 => 'một', 2 => 'hai', 3 => 'ba', 4 => 'bốn',
        5 => 'năm', 6 => 'sáu', 7 => 'bảy', 8 => 'tám', 9 => 'chín',
    );

    public static function addCommas($n) {
        return self::addSeparate($n, ',');
    }

    public static function addDotted($n) {
        return self::addSeparate($n, '.');
    }

    public static function removeCommas($n) {
        return self::removeSeparate($n, ',');
    }

    public static function removeDotted($n) {
        return self::removeSeparate($n, '.');
    }

    public static function addSeparate($n, $o = '.') {
        if ($o == ',') {
            $s = '.';
        } else {
            $s = ',';
        }
        $n = self::removeSeparate($n, $o);
        $x = explode($s, $n);
        $rgx = '/(\d+)(\d{3})/';
        $x1 = $x[0];
        $x2 = count($x) > 1 ? $s + $x[1] : '';
        while (preg_match($rgx, $x1)) {
            $x1 = preg_replace($rgx, "$1{$o}$2", $x1);
        }
        return $x1 . $x2;
    }

    public static function removeSeparate($n, $s = NULL) {
        if (is_null($s)) {
            return preg_replace('/\.|,/m', '', $n);
        }
        return preg_replace('/' . preg_quote($s) . '/m', '', $n);
    }

    public static function cleanNumber($n, $s = false) {
        if (!$s) {
            $s = ',';
        }
        if ($s == ',') {
            $o = '.';
        } else {
            $o = ',';
        }
        $n = self::removeSeparate($n, $o);
        $n = preg_replace('/^[0]+/m', '', $n);
        if (strpos($n, $s) !== false) {
            $n = preg_replace('/[0]+$/m', '', $n);
        }
        return $n;
    }

    public static function readNumber($n, $p = ',') {
        $n = explode($p, self::cleanNumber($n, $p));
        $s = array();
        $chan = isset($n[0]) > 0 ? $n[0] : '';
        $le = isset($n[1]) > 0 ? $n[1] : '';
        if (strlen($chan) > 0) {
            self::readAll($chan, $s);
        }
        if (strlen($le) > 0) {
            self::push('phẩy', $s);
            self::readAll($le, $s);
        }
        return ucfirst(implode('', $s));
    }

    public static function push($v, &$s) {
        if (count($v) > 0 || strlen($v) > 0) {
            if (is_string($v) && strlen($v) > 0) {
                if (empty($s)) {
                    array_push($s, $v);
                } else {
                    array_push($s, ' ');
                    array_push($s, $v);
                }
            } elseif (is_array($v)) {
                foreach ($v as $i) {
                    self::push($i, $s);
                }
            }
        }
    }

    public static function read3($n3, $dl = '', $ex = false) {
        $r = array();
        $donvi = NULL;
        $chuc = NULL;
        $tram = NULL;
        $n3len = strlen($n3);
        if ($n3len === 3) {
            $donvi = (int) $n3[2];
            $chuc = (int) $n3[1];
            $tram = (int) $n3[0];
            if ($tram === 0 && $chuc === 0 && $donvi === 0) {
                if (!empty($dl) && $ex) {
                    //printf('Đọc hàng %s: %s -> %s <br>', $dl, $n3, $dl );
                    return $dl;
                } else {
                    //printf('Đọc hàng %s: %s -> <br>', empty($dl) ? 'đơn vị' : $dl, $n3 );
                    return '';
                }
            }
        } else if ($n3len === 2) {
            $donvi = (int) $n3[1];
            $chuc = (int) $n3[0];
        } else if ($n3len === 1) {
            $donvi = (int) $n3[0];
        }
        if (is_numeric($tram)) {
            array_push($r, self::$nts[$tram]);
            array_push($r, 'trăm');
        }
        if (is_numeric($chuc) && $chuc !== 0) {
            if ($chuc === 1) {
                array_push($r, 'mười');
            } else if (is_numeric($tram) && $chuc === 0) {
                array_push($r, 'lẻ');
            } else {
                array_push($r, self::$nts[$chuc]);
                array_push($r, 'mươi');
            }
        }
        if (is_numeric($donvi)) {
            if ($chuc === 0 && $donvi > 0) {
                array_push($r, 'lẻ');
                array_push($r, self::$nts[$donvi]);
            } else if (is_numeric($chuc) && $chuc !== 1 && $donvi === 1) {
                array_push($r, 'mốt');
            } else if (is_numeric($chuc) && $chuc !== 0 && $donvi === 5) {
                array_push($r, 'lăm');
            } else if ($donvi !== 0 || (!is_numeric($tram) && !is_numeric($chuc))) {
                array_push($r, self::$nts[$donvi]);
            }
        }
        //if( $dl ){ printf('Đọc hàng %s: %s -> %s <br>', $dl, $n3, implode(' ', $r) ); }
        if ($dl && (count($r) > 0 || $ex)) {
            array_push($r, $dl);
        }
        return $r;
    }

    public static function readAll($s, &$r) {
        $s = self::addCommas($s);
        $s = explode(',', $s);
        for ($i = 0, $l = count($s); $i < $l; $i++) {
            $tty = $l - $i - 4;
            $ttrieu = $l - $i - 3;
            $ttram = $l - $i - 2;
            if ($tty % 3 === 0 && $tty >= 0) {
                $ty = $s[$i];
                self::push(self::read3($ty, 'tỷ', !0), $r);
            }
            if ($ttrieu % 3 === 0 && $ttrieu >= 0) {
                $trieu = $s[$i];
                self::push(self::read3($trieu, 'triệu'), $r);
            }
            if ($ttram % 3 === 0 && $ttram >= 0) {
                $nghin = $s[$i];
                self::push(self::read3($nghin, 'nghìn'), $r);
            }
            if ($l - $i === 1) {
                $donvi = $s[$i];
                self::push(self::read3($donvi), $r);
            }
        }
        return $r;
    }

}
