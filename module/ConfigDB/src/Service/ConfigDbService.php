<?php

namespace ConfigDB\Service;

use Zend\Cache\Storage\StorageInterface;
use ConfigDB\Adapter\ConfigAdapterInterface;
use ConfigDB\Model\EntryModel;
use ConfigDB\Model\EntriesModel;

class ConfigDbService {

    const CONFIG_KEY = "config";

    protected $cache;
    protected $adapter;
    protected $config;

    public function __construct(ConfigAdapterInterface $adapter,
            StorageInterface $cache = null) {
        $this->cache = $cache;
        $this->adapter = $adapter;
        
        $this->buildCache();
    }

    public function getConfig($schemadir, $key = "", $userspace = "") {
        
        $schema = explode(".", $schemadir);
        
        $value = &$this->config;

        foreach ($schema as $path) {
            if (empty($value[$path])) {
                throw new \Exception("Schema not exist");
            }

            $value = $value[$path];
        }

        if ($key) {
            return $value[EntriesModel::ENTRIES_KEY][$key];
        }

        return $value[EntriesModel::ENTRIES_KEY];
    }

    public function setConfig($schemadir, $key, $value,
            $value_type = EntryModel::TYPE_STRING, $userspace = "") {

        $success = $this->adapter->set($schemadir, $key, $value, $value_type,
                $userspace);

        if ($success) {
            $this->buildCache();
        }

        return $success;
    }

    private function buildCache() {
        $config = $this->adapter->toArray();

        if ($this->cache instanceof StorageInterface) {
            $this->cache->setItem(self::CONFIG_KEY, $config);
            $this->config = $this->cache->getItem(self::CONFIG_KEY);
        } else {
            $this->config = $config;
        }
        
    }

}
 