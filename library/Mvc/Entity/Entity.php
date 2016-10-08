<?php

namespace Nth\Mvc\Entity;

use Nth\Db\Oracle\Connection;
use Nth\Mvc\Entity\AbstractEntity;

class Entity extends AbstractEntity{
    
    public function __construct(Connection $connection, $table, $input = []) {
        parent::__construct($connection, $table, $input);
    }
    
}