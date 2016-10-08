<?php

namespace Nth\Mvc\Entity;

use Zend\Stdlib\ArrayObject;
use Nth\Db\Oracle\Connection;
use Nth\Db\Oracle\Statement;
use Nth\Db\Oracle\Parameters;

abstract class AbstractEntity extends ArrayObject {

    const SELECT_CONSTRAINTS_SQL = "SELECT cols.COLUMN_NAME, cons.CONSTRAINT_NAME 
        FROM USER_CONSTRAINTS cons, USER_CONS_COLUMNS cols
        WHERE cols.TABLE_NAME = :P_TABLE_NAME
        AND cons.CONSTRAINT_TYPE = :P_CONSTRAINT_TYPE
        AND cons.CONSTRAINT_NAME = cols.CONSTRAINT_NAME";
    const SELECT_COLUMNS_SQL = "SELECT COLUMN_NAME, DATA_TYPE, DATA_LENGTH
        FROM USER_TAB_COLUMNS
        WHERE TABLE_NAME = :P_TABLE_NAME";

    /*
     * Structure of table as array. Its ready when the class init
     * The attributes of column consist of: column_name, data_name, data_type
     */

    private $structure = array(
        'name' => null,
        'columns' => array(),
        'keys' => array(
            'P' => array(
                'name' => null,
                'columns' => array()
            ),
            'U' => array(),
            'F' => array()
        )
    );
    private $connection;
    
    public function __construct(Connection $connection, $table, $input = []) {
        parent::__construct($input, ArrayObject::ARRAY_AS_PROPS);
        $this->setConnection($connection);
        $this->setTableName($table);
        $this->selectPrimaryKey();
        $this->selectColumns();
    }
    
    public function createStatement($sql = null) {
        return new Statement($this->connection, $sql);
    }
    
    public function getConnection() {
        return $this->connection;
    }

    public function setConnection($connection) {
        $this->connection = $connection;
    }

    public function setTableName($tableName) {
        $this->structure['name'] = $tableName;
    }

    public function getTableName() {
        return $this->structure['name'];
    }

    public function selectPrimaryKey() {
        $stmt = $this->createStatement()->prepareCall(self::SELECT_CONSTRAINTS_SQL);
        $stmt->setString('P_TABLE_NAME', $this->getTableName());
        $stmt->setString('P_CONSTRAINT_TYPE', 'P');
        $result = $stmt->executeQuery()->getIterator();
        $P = $this->getPrimaryKey();
        while ($result->valid()) {
            $row = $result->current();
            $P['name'] = $row->CONSTRAINT_NAME;
            array_push($P['columns'], $row->COLUMN_NAME);
            $result->next();
        }
        $this->setPrimaryKey($P);
    }

    protected function setPrimaryKey($P) {
        $this->structure['keys']['P'] = $P;
    }

    protected function getPrimaryKey($option = null) {
        return is_null($option) ? $this->structure['keys']['P'] : $this->structure['keys']['P'][$option];
    }

    protected function selectColumns() {
        $stmt = $this->createStatement()->prepareCall(self::SELECT_COLUMNS_SQL);
        $stmt->setString('P_TABLE_NAME', $this->getTableName());
        $result = $stmt->executeQuery();
        if (!empty($result)) {
            foreach ($result as $row) {
                $name = $row['COLUMN_NAME'];
                $attrs = array(
                    'column_name' => $name,
                    'data_length' => $row['DATA_LENGTH'],
                    'data_type' => $row['DATA_TYPE'],
                );
                $this->setColumn($name, $attrs);
            }
        }
    }

    protected function setColumn($name, array $attrs) {
        $this->structure['columns'][$name] = $attrs;
    }

    public function getColumn($name, $option = null) {
        return is_null($option) ? $this->structure['columns'][$name] : $this->structure['columns'][$name][$option];
    }

    public function fromArray(array $array) {
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                $this->offsetSet($key, $value);
            }
        }
        return $this;
    }

    public function toParameters() {
        $parameters = $this->storage;
        if (!empty($parameters)) {
            $parameter = new Parameters($parameters);
            return $parameter->addPrefix()->toArrayObjectValue();
        }
        return [];
    }

    public function exists() {
        if ($this->emptyPrimaryValue()) {
            return false;
        }
        $stmt = $this->createStatement()->prepareCall("SELECT COUNT(1) AS NUMROWS FROM " . $this->getTableName() . " WHERE " . $this->getPrimaryCondition());
        $this->bindParameter($stmt, $this->getPrimaryKey('columns'));
        $result = $stmt->executeQuery();
        return (int) $result[0]['NUMROWS'] > 0;
    }

    private function getPrimaryCondition() {
        $columns = $this->getPrimaryKey('columns');
        if (empty($columns)) {
            throw new \Exception('Can not find the primary key of ' . $this->getTableName());
        }
        $parameter = new Parameters(array_flip($columns));
        return $parameter->toString(Parameters::SERIALIZE_UPDATE, ' AND ');
    }

    private function bindParameter($stmt, array $columns) {
        foreach ($columns as $name) {
            $dataType = $this->getColumn($name, 'data_type');
            $stmt->setParameter($name, $this->offsetGet($name), $dataType);
        }
        return $stmt->bindParameters();
    }

    private function emptyPrimaryValue() {
        $columns = $this->getPrimaryKey('columns');
        foreach ($columns as $name) {
            if (!$this->offsetExists($name)) {
                return true;
            }
        }
        return false;
    }

    public function select() {
        if ($this->emptyPrimaryValue()) {
            return $this;
        }
        $stmt = $this->createStatement()->prepareCall("SELECT * FROM " . $this->getTableName() . " WHERE " . $this->getPrimaryCondition());
        $this->bindParameter($stmt, $this->getPrimaryKey('columns'));
        $result = $stmt->executeQuery();
        if ($result->offsetGet(0) instanceof ArrayObject) {
            $this->fromArray($result->offsetGet(0)->getArrayCopy());
        }
        return $this;
    }

    public function delete() {
        if ($this->emptyPrimaryValue()) {
            return false;
        }
        $stmt = $this->createStatement()->prepareCall("DELETE FROM " . $this->getTableName() . " WHERE " . $this->getPrimaryCondition());
        $this->bindParameter($stmt, $this->getPrimaryKey('columns'));
        return $this->executeQuery();
    }

    public function insert() {
        //Developing...
    }

    protected function getAllColumnName() {
        $columns = $this->structure['columns'];
        $result = [];
        if (!empty($columns)) {
            foreach ($columns as $column) {
                array_push($result, $column['column_name']);
            }
        }
        return $result;
    }

    public function update() {
        //Developing...
    }

}
