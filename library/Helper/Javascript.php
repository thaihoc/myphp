<?php

namespace Nth\helper;

class Javascript {

    public static function plainText($text) {
        return empty($text) ? '' : '<script type="text/javascript">' . trim($text) . '</script>';
    }

    public static function inc($file) {
        return '<script src="' . $file . '" type="text/javascript"></script>';
    }

    public function detectValue($value, $key = '') {
        $isnum = gettype($value) == 'integer' || (is_numeric($value) && isset($value[0]) && $value[0] != '0');
        $isnull = is_null($value);
        $isneg = strlen($value) > 1 && $value[0] == "!" && is_numeric(substr($value, 1));
        $isbool = is_bool($value);
        if ($isnull) {
            return 'null';
        } elseif ($isbool) {
            return $value ? 'true' : 'false';
        } elseif ($isneg || $isnum) {
            return $value;
        } else {
            return '"' . addslashes(trim($value)) . '"';
        }
    }

    public function getValue($value) {
        if (is_array($value)) {
            if (empty($value)) {
                $result = '[]';
            } else {
                $associative = count(array_diff(array_keys($value), array_keys(array_keys($value))));
                if ($associative) {
                    $construct = array();
                    foreach ($value as $key => $val) {
                        if (is_numeric($key)) {
                            $key = "key_{$key}";
                        }
                        $key = '"' . addslashes($key) . '"';
                        if (is_array($val)) {
                            $val = $this->getValue($val);
                        } else {
                            $val = $this->detectValue($val, $key);
                        }
                        $construct[] = "{$key}: {$val}";
                    }
                    $result = "\n{ \n" . implode(", \n", $construct) . " }";
                } else {
                    $construct = array();
                    foreach ($value as $val) {
                        if (is_array($val)) {
                            $val = $this->getValue($val);
                        } else {
                            $val = $this->detectValue($val);
                        }
                        $construct[] = $val;
                    }
                    $result = "\n[ " . implode(", \n", $construct) . " ]";
                }
            }
        } else {
            $result = $this->detectValue($value);
        }
        return $result;
    }

    public function vars($var, $val = "") {
        $result = '<script language="javascript" type="text/javascript">';
        if (is_array($var)) {
            foreach ($var as $name => $value) {
                $result .= "var " . $name . " = " . $this->getValue($value) . "; \n";
            }
        } else {
            $result .= "var " . $var . " = " . $this->getValue($val) . "; \n";
        }
        $result .= '</script>';
        echo $result;
    }

    public static function redirect($url, $postback = array(), $type = "POST", $frm = 'frmMain') {
        if (empty($postback)) {
            echo \Nth\helper\Html::script("window.location.href='$url'");
        } else {
            $html = '<html><head></head><body>';
            $html .= '<form name="' . $frm . '" action="' . $url . '" method="' . $type . '">';
            foreach ($postback as $key => $val) {
                $html .= \Nth\helper\Html::hidden($key, $val);
            }
            $html .= '</form>';
            $html .= '<script type="text/javascript">document.' . $frm . '.submit();</script>';
            $html .= '</body></html>';
            echo $html;
        }
    }

}
