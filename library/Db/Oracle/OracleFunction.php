<?php

namespace Nth\Db\Oracle;

use Nth\Db\Oracle\AbstractStatement;

class OracleFunction extends AbstractStatement {

    const RESULT_PARAMETER_NAME = 'P_ORAFUNC_RESULT';

    private $name;
    private $returnType;

    public function __construct(Connection $connection, $name = null, $parameters = [], $returnType = null, $data = []) {
        parent::__construct($connection, null, $parameters, $data);
        $this->setName($name);
        $this->setReturnType($returnType);
    }
    
    public function getResultParameter() {
        return $this->getParameter(self::RESULT_PARAMETER_NAME);
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getReturnType() {
        return $this->returnType;
    }

    public function setReturnType($returnType) {
        $this->setParameter(self::RESULT_PARAMETER_NAME, null, $returnType, -1, 'OUT');
        $this->returnType = $returnType;
        return $this;
    }

    public function prepareCall($name = null, $parameters = null, $returnType = null, $data = null) {
        if (!is_null($name)) {
            $this->setName($name);
        }
        if (!is_null($parameters)) {
            $this->setParameters($parameters);
        }
        if (!is_null($returnType)) {
            $this->setReturnType($returnType);
        }
        if (!is_null($data)) {
            $this->setData($data);
        }
        $sql = sprintf('BEGIN %s := %s(%s); END;'
                , $this->getResultParameter()->getNameToBind()
                , $this->getName()
                , $this->getParameters()->toString());
        parent::prepareCall($sql);
        return $this;
    }

    public function execute() {
        oci_execute($this->getStmt(), $this->getConnection()->getExecuteMode());
        return $this;
    }
    
    public function getResult($row = null, $col = null) {
        if (Types::isCursor($this->returnType)) {
            return $this->getCursor(self::RESULT_PARAMETER_NAME, $row, $col);
        }
        return $this->getParameter(self::RESULT_PARAMETER_NAME)->getValue();
    }
    
    public static function isResultParameter($name) {
        return self::RESULT_PARAMETER_NAME === $name;
    }

}
