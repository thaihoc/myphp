<?php

namespace Nth\Db\Oracle;

use Nth\Nth;
use Nth\Db\Oracle\AbstractStatement;

class Statement extends AbstractStatement {
    
    public function executeQuery() {
        $r = oci_execute($this->getStmt(), $this->getConnection()->getExecuteMode());
        if (!$r && $this->getDebug()) {
            Nth::dump($this->getSql());
        }
        if ('ArrayObject' === $this->getFetchType()) {
            $result = $this->fetchArrayObject($this->getStmt());
        } else {
            $result = $this->fetchArray($this->getStmt());
        }
        $this->close();
        return $result;
    }
 
    public function executeUpdate() {
        $r = oci_execute($this->getStmt(), $this->getConnection()->getExecuteMode());
        if (!$r && $this->getDebug()) {
            Nth::dump($this->getSql());
        }
        $result = oci_num_rows($this->getStmt());
        $this->close();
        return $result;
    }

}
