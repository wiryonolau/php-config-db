<?php

namespace ConfigDB\Options;

use Interop\Container\ContainerInterface;
use Zend\Stdlib\AbstractOptions;
use Zend\Db\Adapter\Adapter as ZendDbAdapter;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;

class ModuleOptions extends AbstractOptions {

    protected $config_adapter;
    protected $database_adapter;
    protected $database_table = "configdb";
    protected $cache_config = true;
    protected $cache_adapter;
    protected $default_userspace = "global";

    public function __construct($options = null) {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

    public function setConfigAdapter($adapter) {
        $this->config_adapter = (string) $adapter;
    }

    public function setDatabaseAdapter($adapter) {
        if (is_array($adapter)) {
            $adapter = new ZendDbAdapter($adapter);
        }

        if (!$adapter instanceof ZendDbAdapter) {
            throw new \Exception("No adapter set");
        }

        $this->database_adapter = $adapter;
    }

    public function setDatabaseTable($table) {
        $this->database_table = (string) $table;
    }

    public function setCacheConfig($enabled) {
        $this->cache_config = (bool) $enabled;
    }

    public function setCacheAdapter($config) {

        if ($config == false) {
            $cache = null;
        } else if (is_array($config)) {
            $cache = StorageFactory::factory($config);
        } else if ($config instanceof StorageInterface) {
            $cache = $config;
        }

        $this->cache_adapter = $cache;
    }

    public function setDefaultUserspace($userspace) {
        $this->default_userspace = (string) $userspace;
    }

    public function getConfigAdapter(ContainerInterface $container = null) {
        if (is_null($container)) {
            return $this->config_adapter;
        }

        return $container->get($this->config_adapter);
    }

    public function getDatabaseAdapter() {
        return $this->database_adapter;
    }

    public function getDatabaseTable() {
        return $this->database_table;
    }

    public function getCacheConfig() {
        return $this->cache_config;
    }

    public function getCacheAdapter() {
        return $this->cache_adapter;
    }

    public function getDefaultUserspace() {
        return $this->default_userspace;
    }

}
