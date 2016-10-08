<?php

namespace Nth\Db\Oracle;

use Exception;

class Connection {

    const DEFAULT_SESSION = OCI_DEFAULT;
    const SYSOPER_SESSION = OCI_SYSOPER;
    const SYSDBA_SESSION = OCI_SYSDBA;

    /**
     * @property $username The Oracle user name
     */
    private $username;

    /**
     * @property $password The password for username
     */
    private $password;

    /**
     * @property $hostName The name of host where Oracle installed
     */
    private $hostName;

    /**
     * @property $port The Oracle listener port
     */
    private $port;

    /**
     * @property $serviceName The Oracle SID
     */
    private $serviceName;

    /**
     * @property $serverType 
     */
    private $serverType;

    /**
     * @property $instanceName
     */
    private $instanceName;

    /**
     * @property $connectionString
     * for Oracle 10g: [//]host_name[:port][/service_name]
     * for Oracle 11g: [//]host_name[:port][/service_name][:server_type][/instance_name]
     */
    private $connectionString;

    /**
     * @property $characterSet The character set used by the Oracle Client libraries
     * set to AL32UTF8 as default
     */
    private $characterSet;

    /**
     * @property $sessionMode Set to OCI_DEFAULT as default
     */
    private $sessionMode;
    
    /**
     * @property $executeMode The OCI execute mode
     */
    private $executeMode;

    /**
     * @property $resource An instance of __CLASS__ after it was initilized
     */
    private $resource;

    public function __construct($username, $password, $connectionString, $characterSet = 'AL32UTF8', $sessionMode = self::DEFAULT_SESSION) {
        $this->username = $username;
        $this->password = $password;
        $this->connectionString = $connectionString;
        $this->sessionMode = $sessionMode;
        $this->characterSet = $characterSet;
        $this->executeMode = OCI_COMMIT_ON_SUCCESS;
    }

    public function connect() {
        $resource = oci_connect($this->getUsername()
                , $this->getPassword()
                , $this->getConnectionString()
                , $this->getCharacterSet()
                , $this->getSessionMode());
        if (!$resource) {
            $e = oci_error();
            throw new Exception(htmlentities($e['message'], ENT_QUOTES));
        }
        return $this->setResource($resource);
    }

    public function reconnect() {
        $this->close();
        return $this->connect();
    }

    public function connected() {
        return gettype($this->resource) === 'resource';
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getHostName() {
        return $this->hostName;
    }

    public function getPort() {
        return $this->port;
    }

    public function getServiceName() {
        return $this->serviceName;
    }

    public function getServerType() {
        return $this->serverType;
    }

    public function getInstanceName() {
        return $this->instanceName;
    }

    public function getConnectionString() {
        if ($this->connectionString) {
            return $this->connectionString;
        }
        $connectionString = sprintf('//%s:%s/%s', $this->hostName, $this->port, $this->serviceName);
        if ($this->serverType) {
            $connectionString .= ':' . $this->serverType;
        }
        if ($this->instanceName) {
            $connectionString .= '/' . $this->instanceName;
        }
        return $connectionString;
    }

    public function getCharacterSet() {
        return $this->characterSet;
    }

    public function getSessionMode() {
        return $this->sessionMode;
    }

    public function getResource() {
        return $this->resource;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function setHostName($hostName) {
        $this->hostName = $hostName;
        return $this;
    }

    public function setPort($port) {
        $this->port = $port;
        return $this;
    }

    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
        return $this;
    }

    public function setServerType($serverType) {
        $this->serverType = $serverType;
        return $this;
    }

    public function setInstanceName($instanceName) {
        $this->instanceName = $instanceName;
        return $this;
    }

    public function setConnectionString($connectionString) {
        $this->connectionString = $connectionString;
        return $this;
    }

    public function setCharacterSet($characterSet) {
        $this->characterSet = $characterSet;
        return $this;
    }

    public function setSessionMode($sessionMode) {
        $this->sessionMode = $sessionMode;
        return $this;
    }
    
    public function turnOffAutoCommit() {
        $this->executeMode = OCI_NO_AUTO_COMMIT;
        return $this;
    }

    public function turnOnAutoCommit() {
        $this->executeMode = OCI_COMMIT_ON_SUCCESS;
        return $this;
    }
    
    public function getExecuteMode() {
        return $this->executeMode;
    }

    public function setExecuteMode($executeMode) {
        $this->executeMode = $executeMode;
    }

    protected function setResource($resource) {
        $this->resource = $resource;
        return $this;
    }
    
    public function commit() {
        oci_commit($this->resource);
        return $this;
    }
    
    public function rollback() {
        oci_rollback($this->resource);
        return $this;
    }

    public function close() {
        oci_close($this->resource);
        return $this;
    }

}
