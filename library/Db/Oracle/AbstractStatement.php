<?php

namespace Nth\Db\Oracle;

use DateTime;
use Exception;
use Nth\Db\Oracle\Connection;
use Nth\Db\Oracle\Parameter;
use Nth\Db\Oracle\Parameters;
use Nth\Db\Oracle\Types;
use Nth\Helper\Convertor;
use Zend\Stdlib\ArrayObject;

abstract class AbstractStatement {

    private $connection;
    private $stmt;
    private $sql;
    private $parameters;
    private $fetchMode;
    private $data;
    private $fetchType;
    private $debug;

    public function __construct(Connection $connection, $sql = null, $parameters = [], $data = []) {
        $this->connection = $connection;
        $this->sql = $sql;
        $this->setParameters($parameters);
        $this->setData($data);
        $this->fetchMode = OCI_ASSOC + OCI_RETURN_NULLS;
        $this->fetchType = 'ArrayObject'; //Array or ArrayObject
        $this->debug = false;
    }
    
    public function getDebug() {
        return $this->debug;
    }

    public function setDebug($debug) {
        $this->debug = $debug;
    }
    
    public function getFetchType() {
        return $this->fetchType;
    }

    public function setFetchType($fetchType) {
        $this->fetchType = $fetchType;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
        $this->getParameters()->bindValues($data);
        return $this;
    }

    public function getFetchMode() {
        return $this->fetchMode;
    }

    public function setFetchMode($fetchMode) {
        $this->fetchMode = $fetchMode;
    }

    public function getConnection() {
        return $this->connection;
    }

    protected function getStmt() {
        return $this->stmt;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function &getParameter($name) {
        return $this->getParameters()->getParameter($name);
    }

    public function setConnection(Connection $connection) {
        $this->connection = $connection;
        return $this;
    }

    protected function setStmt($stmt) {
        $this->stmt = $stmt;
        return $this;
    }

    protected function setParameters($parameters) {
        if (!$parameters instanceof Parameters) {
            $parameters = new Parameters($parameters);
        }
        $this->parameters = $parameters;
        return $this;
    }

    public function setParameter($name, $value, $type, $maxlength = -1, $modes = 'IN OUT') {
        $parameter = new Parameter($name, $value, $type, $maxlength, $modes);
        $this->getParameters()->setParameter($name, $parameter);
        return $this;
    }

    public function getSql() {
        return $this->sql;
    }

    public function setSql($sql) {
        $this->sql = $sql;
        return $this;
    }

    public function prepareCall($sql = null) {
        if (!is_null($sql)) {
            $this->setSql($sql);
        }
        $this->parseSql();
        return $this;
    }
    
    public function parseSql() {
        $this->stmt = oci_parse($this->getConnection()->getResource(), $this->getSql());
        return $this;
    }

    public function setInt($name, $value) {
        if (is_null($value) || $value === '') {
            return $this->setString($name, $value);
        }
        return $this->setParameter($name, $value, Types::INTEGER)->bindParameter($name);
    }

    public function getInt($name) {
        return (int) $this->getParameter($name)->getValue();
    }

    public function setFloat($name, $value) {
        if (is_null($value) || $value === '') {
            return $this->setString($name, $value);
        }
        return $this->setParameter($name, $value, Types::FLOAT)->bindParameter($name);
    }

    public function getFloat($name) {
        return (float) $this->getParameter($name)->getValue();
    }

    public function setLong($name, $value) {
        if (is_null($value) || $value === '') {
            return $this->setString($name, $value);
        }
        $this->setParameter($name, $value, Types::LONG)->bindParameter($name);
    }

    public function getLong($name) {
        return (int) $this->getParameter($name)->getValue();
    }

    public function setString($name, $value, $maxlength = 3000) {
        return $this->setParameter($name, $value, Types::NVARCHAR2, $maxlength)->bindParameter($name);
    }

    public function getString($name) {
        return (string) $this->getParameter($name)->getValue();
    }

    public function setByte($name, $value, $maxlength = 255) {
        $this->setParameter($name, $value, Types::BYTE, $maxlength)->bindParameter($name);
    }

    public function getByte($name) {
        return (string) $this->getParameter($name)->getValue();
    }

    public function setClob($name, $value) {
        $descriptor = oci_new_descriptor($this->getConnection()->getResource(), OCI_D_LOB);
        $this->setParameter($name, $descriptor, Types::NCLOB)->bindParameter($name);
        $descriptor->writeTemporary($value);
        return $this;
    }

    public function getClob($name) {
        return $this->getParameter($name)->getValue();
    }

    public function setBlob($name, $value) {
        $descriptor = oci_new_descriptor($this->getConnection()->getResource(), OCI_D_LOB);
        $this->setParameter($name, $descriptor, Types::BLOB)->bindParameter($name);
        $descriptor->writeTemporary($value);
        return $this;
    }

    public function getBlob($name) {
        return $this->getParameter($name)->getValue();
    }

    public function setDate($name, $value) {
        return $this->setParameter($name, $value, Types::DATE, 100)->bindParameter($name);
    }

    public function getDate($name, $format = 'd/M/Y') {
        $dateString = $this->getParameter($name)->getValue();
        return DateTime::createFromFormat($format, $dateString);
    }

    public function setVarray($name, $value, $type) {
        $collection = oci_new_collection($this->getConnection()->getResource(), $type);
        if (!empty($value)) {
            foreach ((array) $value as $item) {
                $collection->append($item);
            }
        }
        return $this->setParameter($name, $collection, $type)->bindParameter($name);
    }

    public function getVarray($name) {
        return $this->getParameter($name)->getValue();
    }

    public function setCursor($name, $value) {
        if (gettype($value) !== 'resource') {
            $value = oci_new_cursor($this->getConnection()->getResource());
        }
        return $this->setParameter($name, $value, Types::CURSOR)->bindParameter($name);
    }

    public function getCursor($name, $row = null, $col = null) {
        $value = $this->getParameter($name)->getValue();
        if (gettype($value) === 'resource') {
            oci_execute($value, $this->getConnection()->getExecuteMode());
            $value = $this->fetchArrayObject($value);
            $this->setParameter($name, $value, Types::CURSOR);
        }
        $fetchType = $this->getFetchType();
        if ($fetchType === 'ArrayObject') {
            $data = clone $value;
            if (!is_null($row)) {
                $data = $data->offsetGet($row);
                if (!$data instanceof ArrayObject) {
                    $data = Convertor::toArrayObject([]);
                }
                if (!is_null($col)) {
                    $data = $data instanceof ArrayObject ? $data->offsetGet($col) : null;
                }
            }
            return $data;
        }
        $data = [];
        $iterator = $value->getIterator();
        while ($iterator->valid()) {
            $current = $iterator->current();
            $data[] = $current->getArrayCopy();
            $iterator->next();
        }
        if (!is_null($row)) {
            if (is_array($data[$row])) {
                if (!is_null($col)) {
                    return isset($data[$row][$col]) ? $data[$row][$col] : null;
                }
                return $data[$row];
            }
            return [];
        }
        return $data;
    }
    
    public function getFirstCursor($row = null, $col = null) {
        $result = null;
        $iterator = $this->getParameters()->toArrayObject()->getIterator();
        while ($iterator->valid()) {
            $parameter = $iterator->current();
            if (Types::isCursor($parameter->getType())) {
                $result = $this->getCursor($parameter->getName(), $row, $col);
                break;
            }
            $iterator->next();
        }
        return $result;
    }

    public function bindParameter($name) {
        $this->getParameter($name)->bindTo($this->stmt);
        return $this;
    }

    protected function fuzzyBindParameter(Parameter $parameter) {
        $name = $parameter->getName();
        $type = $parameter->getType();
        $value = $parameter->getValue();
        if (Types::isString($type)) {
            $this->setString($name, $value);
        } elseif (Types::isInt($type) || Types::isNumber($type)) {
            $this->setInt($name, $value);
        } elseif (Types::isCursor($type)) {
            $this->setCursor($name, $value);
        } elseif (Types::isClob($type) || Types::isNclob($type)) {
            $this->setClob($name, $value);
        } elseif (Types::isDate($type)) {
            $this->setDate($name, $value);
        } elseif (Types::isVarray($type)) {
            $this->setVarray($name, $value, $type);
        } elseif (Types::isFloat($type)) {
            $this->setFloat($name, $value);
        } elseif (Types::isBlob($type)) {
            $this->setBlob($name, $value);
        } elseif (Types::isByte($type)) {
            $this->setByte($name, $value);
        } elseif (Types::isLong($type)) {
            $this->setLong($name, $value);
        } else {
            throw new Exception(sprintf('Unknown Oracle Type %s to bind %s parameter in %s.', $type, $name, $this->sql));
        }
        return $this;
    }

    public function registerOutParameter($name, $type) {
        return $this->fuzzyBindParameter(new Parameter($name, null, $type));
    }

    public function bindParameters() {
        $iterator = $this->getParameters()->toArrayObject()->getIterator();
        while ($iterator->valid()) {
            $parameter = $iterator->current();
            $this->fuzzyBindParameter($parameter);
            $iterator->next();
        }
        return $this;
    }

    protected function fetchArray($stmt) {
        $data = [];
        while ($row = oci_fetch_array($stmt, $this->getFetchMode())) {
            $data[] = $row;
        }
        return $data;
    }
    
    protected function fetchArrayObject($stmt) {
        $data = [];
        while ($row = oci_fetch_array($stmt, $this->getFetchMode())) {
            $data[] = Convertor::toArrayObject($row);
        }
        return Convertor::toArrayObject($data);
    }

    public function close() {
        oci_free_statement($this->stmt);
    }

}
