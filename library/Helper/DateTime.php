<?php

namespace Nth\helper;

use Nth\Helper\Html;

class DateTime {

    public static function isLeapYear($Y) {
        if ($Y % 4 == 0) {
            if ($Y % 100 == 0) {
                return $Y % 400 == 0 ? 1 : 0;
            }
            return 1;
        }
        return 0;
    }

    public static function daysInMonth($n, $Y) {
        $t = 0;
        if (in_array($n, array(1, 3, 5, 7, 8, 10, 12))) {
            $t = 31;
        } elseif (in_array($n, array(4, 6, 9, 11))) {
            $t = 30;
        } else if ($n == 2) {
            self::isLeapYear($Y) ? $t = 29 : $t = 28;
        }
        return $t;
    }

    public static function clarification($dateString, $format = 'd/m/Y H:i:s') {
        $result = '';
        $date = DateTime::createFromFormat($format, $dateString);
        $now = new DateTime('NOW');
        $diff = $now->format('Y') - $date->format('Y');
        if ($diff === 0) {
            $diff = $now->format('n') - $date->format('n');
            if ($diff === 0) {
                $diff = $now->format('j') - $date->format('j');
                if ($diff === 0) {
                    $diff = $now->format('G') - $date->format('G');
                    if ($diff === 0) {
                        $diff = $now->format('i') - $date->format('i');
                        if ($diff === 0) {
                            $result = 'Mới đây';
                        } elseif ($diff > 0) {
                            $result = $diff . ' phút trước';
                        } else {
                            $result = $diff . ' phút sau';
                        }
                    } elseif ($diff > 0) {
                        if ($diff === 1) {
                            $diff = $now->format('i') + 60 - $date->format('i');
                            if ($diff < 60) {
                                $result = $diff . ' phút trước';
                            } else {
                                $result = '1 giờ trước';
                            }
                        } else {
                            $result = $diff . ' giờ trước';
                        }
                    } else {
                        $result = abs($diff) + ' giờ sau';
                    }
                } elseif ($diff > 0) {
                    if ($diff === 1) {
                        $result = 'Hôm qua';
                    } elseif ($diff === 2) {
                        $result = 'Hôm kia';
                    } else {
                        $result = $diff . ' ngày trước';
                    }
                } elseif ($diff < 0) {
                    $diff = abs($diff);
                    if ($diff === 1) {
                        $result = 'Ngày mai';
                    } else if (diff === 2) {
                        $result = 'Ngày mốt';
                    } else {
                        $result = $diff + ' ngày sau';
                    }
                }
            } elseif ($diff > 0) {
                if ($diff === 1) {
                    $diff = $now->format('j') + self::daysInMonth($date->format('n'), $date->format('Y')) - $date->format('j');
                    if ($diff < 28) {
                        if ($diff === 1) {
                            $result = 'Hôm qua';
                        } elseif ($diff === 2) {
                            $result = 'Hôm kia';
                        } else {
                            $result = $diff . ' ngày trước';
                        }
                    } else {
                        $result = '1 tháng trước';
                    }
                } else {
                    $result = $diff . ' tháng trước';
                }
            } else {
                $result = abs($diff) . ' tháng sau';
            }
        } elseif ($diff > 0) {
            if ($diff === 1) {
                $diff = $now->format('n') + 12 - $date->format('n');
                if ($diff < 12) {
                    $result = $diff . ' tháng trước';
                } else {
                    $result = '1 năm trước';
                }
            } else {
                $result = $diff . ' năm trước';
            }
        } else {
            $result = abs($diff) . ' năm sau';
        }
        return $result;
    }

    /**
     * Ham chuyen kieu datetime thanh kieu int 
     * (Luu y: neu khong parse duoc thi tra ve Unix Epoch timestamp: Janualy 1, 1790 00:00:00)
     * @param: except is array in (n:thang, j:ngay, Y:name). Truong hop khong parse duoc bien doi cac gia tri tuong ung do thanh 0
     */
    public static function dateToInt($datetime, $format, $except = array()) {
        $datetime = DateTime::createFromFormat("|" . $format, $datetime);

        $params['H'] = $datetime->format("H");
        $params['i'] = $datetime->format("i");
        $params['s'] = $datetime->format("s");
        $params['n'] = in_array('n', $except) ? 0 : $datetime->format("n");
        $params['j'] = in_array('j', $except) ? 0 : $datetime->format("j");
        $params['Y'] = in_array('Y', $except) ? 0 : $datetime->format("Y");

        if ($datetime != "")
            return $datetime = mktime(
                    $params['H'], $params['i'], $params['s'], $params['n'], $params['j'], $params['Y']);

        return $datetime = 0;
    }

    public static function intToDate($datetime, $format) {
        return date($format, $datetime);
    }

    public static function createDateList($date_start, $date_end, $format_input = 'd-m-Y', $format_output = 'd-m-Y', $separator = ', ') {
        $result = array();
        $date_start = self::dateToInt($date_start, $format_input);
        $date_end = self::dateToInt($date_end, $format_input);
        while ($date_start <= $date_end) {
            $result[] = self::intToDate($date_start, $format_output);
            $date_start+=60 * 60 * 24;
        }
        if (!empty($separator)) {
            return "'" . implode("'" . $separator . "'", $result) . "'";
        }
        return $result;
    }

    public static function getDateTimeOfWeek($format = "d/M/Y", $rangeOfWeek = "Mon", $week = 0) {
        $day = strtotime('last monday');
        if ((date('j') - date('j', $day)) === 7)
            $day = strtotime('now');

        $day += 60 * 60 * 24 * 7 * (int) $week;
        switch (strtolower($rangeOfWeek)) {
            case 'mon':
                $rangeOfWeek = 0;
                break;
            case 'tue':
                $rangeOfWeek = 1;
                break;
            case 'wed':
                $rangeOfWeek = 2;
                break;
            case 'thu':
                $rangeOfWeek = 3;
                break;
            case 'fri':
                $rangeOfWeek = 4;
                break;
            case 'sat':
                $rangeOfWeek = 5;
                break;
            case 'sun':
                $rangeOfWeek = 6;
                break;
            default:
                break;
        }
        $day += 60 * 60 * 24 * (int) $rangeOfWeek;
        return date($format, $day);
    }

    public static function getFulldayFrom(&$from) {
        if (!empty($from)) {
            $from = trim($from, ' /');
            if (DateTime::createFromFormat('m/Y', $from)) {
                $from = '01/' . $from;
            } elseif (DateTime::createFromFormat('Y', $from)) {
                $from = '01/01/' . $from;
            }
            $dateObj = DateTime::createFromFormat('d/m/Y', $from);
            $from = $dateObj->format('d/m/Y');
        }
        return $from;
    }

    public static function getFulldayTo(&$to) {
        if (!empty($to)) {
            $to = trim($to, ' /');
            if ($dateObj = DateTime::createFromFormat('m/Y', $to)) {
                $to = cal_days_in_month(CAL_GREGORIAN, $dateObj->format('n'), $dateObj->format('Y')) . '/' . $to;
            } elseif (DateTime::createFromFormat('Y', $to)) {
                $to = '31/12/' . $to;
            }
            $dateObj = DateTime::createFromFormat('d/m/Y', $to);
            $to = $dateObj->format('d/m/Y');
        }
        return $to;
    }

    public function getHtmlOptionsListDays($selected = null) {
        $arrDay = array();
        for ($i = 1; $i <= 31; $i++) {
            $arrDay[] = $i;
        }
        return Html::getOptions($arrDay, null, null, $selected);
    }

    public function getHtmlOptionsListMonth($selected = null) {
        $arrMonth = array();
        for ($i = 1; $i <= 12; $i++) {
            $arrMonth[] = $i;
        }
        return Html::getOptions($arrMonth, null, null, $selected);
    }

    public function getHtmlOptionsListYear($selected = null, $min = 2000, $max = false) {
        if (!$max) {
            $max = date('Y');
        }
        $arrYear = array();
        for ($i = $min; $i <= $max; $i++) {
            $arrYear[] = $i;
        }
        return Html::getOptions($arrYear, null, null, $selected);
    }

}
