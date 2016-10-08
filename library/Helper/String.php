<?php

namespace Nth\helper;

class String {

    public static function rmMarks($str) {
        $matrix = array(
            "a" => "à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ",
            "e" => "è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ",
            "i" => "ì|í|ị|ỉ|ĩ",
            "o" => "ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ",
            "u" => "ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ",
            "y" => "ỳ|ý|ỵ|ỷ|ỹ",
            "d" => "đ",
            "A" => "À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ",
            "E" => "È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ",
            "I" => "Ì|Í|Ị|Ỉ|Ĩ",
            "O" => "Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ",
            "U" => "Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ",
            "Y" => "Ỳ|Ý|Ỵ|Ỷ|Ỹ",
            "D" => "Đ",
        );
        foreach ($matrix as $rep => $reg) {
            $str = preg_replace("/$reg/", $rep, $str);
        }
        return $str;
    }

    public static function cleanSpecialChars($url) {
        return preg_replace('/[^A-Za-z0-9\s]/', '', $url);
    }

    public static function limitString($string, $length = 30, $end = "...", $encoding = 'UTF-8') {
        return (mb_strlen($string, $encoding) > ($length)) ? mb_substr($string, 0, ($length - 3), $encoding) . $end : $string;
    }

    public static function upper($str, $lstchrs = false) {
        if (!empty($lstchrs)) {
            if ($lstchrs === true) {
                $lstchrs = ' ';
            }
            return mb_strtoupper(trim($str, $lstchrs), 'UTF-8');
        }
        return mb_strtoupper($str, 'UTF-8');
    }

    public static function lower($str, $lstchrs = false) {
        if (!empty($lstchrs)) {
            if ($lstchrs === true) {
                $lstchrs = ' ';
            }
            return mb_strtolower(trim($str, $lstchrs), 'UTF-8');
        }
        return mb_strtolower($str, 'UTF-8');
    }

    public static function nthHash($pwd) {
        $pwdgen = $pwd;
        $pwdpre = 'HCCTGAOSUPHH914AM2014513'; //Don't change this value
        $j = mb_strlen($pwd);
        $k = mb_strlen($pwdpre);
        if ($j > 0) {
            if ($k > 0) {
                $pwdgen = substr_replace($pwdpre, $pwd[$j - 1], 0, 1);
                for ($i = 0; $i < $j; $i++) {
                    $pwdgen = substr_replace($pwdgen, $pwd[$i], $j - $i, 1);
                }
            }
            $pwdgen = md5(md5($pwdgen));
        }
        return $pwdgen;
    }

    public static function fromClob($obj, $max = 500000, $metime = 300) {
        if (is_a($obj, 'OCI-Lob')) {
            return $obj->read($max);
        }
        if (ini_get('max_execution_time') < $metime) {
            set_time_limit($metime);
        }
        return $obj;
    }

}
