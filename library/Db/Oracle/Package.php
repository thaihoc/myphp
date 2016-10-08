<?php

namespace Nth\Db\Oracle;

use Nth\Db\Oracle\Connection;
use Nth\Db\Oracle\OracleFunction;
use Nth\Db\Oracle\StoreProcedure;

class Package {

    private $connection;
    private $name;
    private $storeProcedure;
    private $oracleFunction;
    private $result;

    public function __construct(Connection $connection, $name = null) {
        $this->connection = $connection;
        $this->name = $name;
        $this->storeProcedure = new StoreProcedure($connection);
        $this->oracleFunction = new OracleFunction($connection);
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getName() {
        return $this->name;
    }

    public function setConnection($connection) {
        $this->connection = $connection;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getStoreProcedure() {
        return $this->storeProcedure;
    }

    public function getOracleFunction() {
        return $this->oracleFunction;
    }

    public function setStoreProcedure(StoreProcedure $storeProcedure) {
        $this->storeProcedure = $storeProcedure;
        return $this;
    }

    public function setOracleFunction(OracleFunction $oracleFunction) {
        $this->oracleFunction = $oracleFunction;
        return $this;
    }

    public function executeSP($name, $parameters = [], $data = []) {
        $this->result = $this->getStoreProcedure()
                ->prepareCall($this->getName() . '.' . $name, $parameters, $data)
                ->bindParameters()
                ->execute();
        return $this;
    }

    public function executeF($name, $parameters, $returnType, $data = []) {
        $this->result = $this->getOracleFunction()
                ->prepareCall($this->getName() . '.' . $name, $parameters, $returnType, $data)
                ->bindParameters()
                ->execute();
        return $this;
    }
    
    public function getResult() {
        return $this->result;
    }

    protected function setResult($result) {
        $this->result = $result;
    }
    
    public function getDefaultResult($row = null, $col = null) {
        if ($this->result instanceof OracleFunction) {
            return $this->getResult()->getResult($row, $col);
        }
        return $this->getResult()->getDefaultResult($row, $col);
    }



}
