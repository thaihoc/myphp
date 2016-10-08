<?php

namespace Nth\Db\Oracle;

use Exception;

class Types {

    const INTEGER = 'INTEGER';
    const NUMBER = 'NUMBER';
    const VARCHAR2 = 'VARCHAR2';
    const NVARCHAR2 = 'NVARCHAR2';
    const CLOB = 'CLOB';
    const NCLOB = 'NCLOB';
    const DATE = 'DATE';
    const CURSOR = 'CURSOR';
    const VARRAY = 'VARRAY';
    const FLOAT = 'FLOAT';
    const BLOB = 'BLOB';
    const BYTE = 'BYTE';
    const LONG = 'LONG';

    public static $rolesMap = array(
        self::INTEGER => array('INT', 'INTEGER'),
        self::NUMBER => array('NUMBER'),
        self::VARCHAR2 => array('VARCHAR2'),
        self::NVARCHAR2 => array('NVARCHAR2'),
        self::CLOB => array('CLOB'),
        self::NCLOB => array('NCLOB'),
        self::DATE => array('DATE'),
        self::CURSOR => array('CURSOR', 'MYCUR'),
        self::VARRAY => array('VARRAY', 'LIST_OF_NUMBERS', 'LIST_OF_NVARCHAR2S'),
        self::FLOAT => array('FLOAT'),
        self::BLOB => array('BLOB'),
        self::BYTE => array('BYTE'),
        self::LONG => array('LONG'),
    );

    public static function is($rtype, $type) {
        if (!isset(self::$rolesMap[$rtype])) {
            throw new Exception('The first argument must be an Oracle Type');
        }
        return in_array($type, self::$rolesMap[$rtype]);
    }

    public static function isInt($type) {
        return $type === self::INTEGER || self::is(self::INTEGER, $type);
    }

    public static function isFloat($type) {
        return $type === self::FLOAT || self::is(self::FLOAT, $type);
    }

    public static function isLong($type) {
        return $type === self::LONG || self::is(self::LONG, $type);
    }

    public static function isByte($type) {
        return $type === self::BYTE || self::is(self::BYTE, $type);
    }

    public static function isNumber($type) {
        return $type === self::NUMBER || self::is(self::NUMBER, $type);
    }

    public static function isVarchar2($type) {
        return $type === self::VARCHAR2 || self::is(self::VARCHAR2, $type);
    }

    public static function isNvarchar2($type) {
        return $type === self::NVARCHAR2 || self::is(self::NVARCHAR2, $type);
    }

    public static function isString($type) {
        return self::isVarchar2($type) || self::isNvarchar2($type);
    }

    public static function isVarray($type) {
        return $type === self::VARRAY || self::is(self::VARRAY, $type);
    }

    public static function isCursor($type) {
        return $type === self::CURSOR || self::is(self::CURSOR, $type);
    }

    public static function isClob($type) {
        return $type === self::CLOB || self::is(self::CLOB, $type);
    }

    public static function isNclob($type) {
        return $type === self::NCLOB || self::is(self::NCLOB, $type);
    }

    public static function isBlob($type) {
        return $type === self::BLOB || self::is(self::BLOB, $type);
    }

    public static function isDate($type) {
        return $type === self::DATE || self::is(self::DATE, $type);
    }

}
