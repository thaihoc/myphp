<?php

namespace Nth\Db\Oracle;

use Nth\Nth;
use Nth\Db\Oracle\Connection;
use Nth\Db\Oracle\AbstractStatement;

class StoreProcedure extends AbstractStatement {

    private $name;

    public function __construct(Connection $connection, $name = null, $parameters = [], $data = []) {
        parent::__construct($connection, null, $parameters, $data);
        $this->setName($name);
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function prepareCall($name = null, $parameters = null, $data = null) {
        if (!is_null($name)) {
            $this->setName($name);
        }
        if (!is_null($parameters)) {
            $this->setParameters($parameters);
        }
        if (!is_null($data)) {
            $this->setData($data);
        }
        $sql = sprintf('BEGIN %s(%s); END;', $this->getName(), $this->getParameters()->toString());
        parent::prepareCall($sql);
        return $this;
    }

    public function execute() {
        $r = oci_execute($this->getStmt(), $this->getConnection()->getExecuteMode());
        if (!$r && $this->getDebug()) {
            Nth::dump($this->getName());
            Nth::dump($this->getParameters()->toArrayObjectValue());
        }
        return $this;
    }
    
    /**
     * @method getDefaultResult if exists cursor parameters then return first cursor data
     * else return all parameters as an instanceof ArrayObject of parameter values
     * @param $row The row index for getting a record
     * @param $col The name of column for getting
     */
    public function getDefaultResult($row = null, $col = null) {
        $result = $this->getFirstCursor($row, $col);
        if (is_null($result)) {
            $result = $this->getParameters()->toArrayObjectValue();
            if (!is_null($row)) {
                $result = $result->offsetGet($row);
            }
        }
        return $result;
    }

}
