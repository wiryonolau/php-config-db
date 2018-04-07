<?php

namespace ConfigDB\Service;

use Zend\Cache\Storage\StorageInterface;
use ConfigDB\Adapter\ConfigAdapterInterface;
use ConfigDB\Model\EntryModel;
use ConfigDB\Model\EntriesModel;

class ConfigDbService {

    protected $cache;
    protected $adapter;
    protected $default_userspace;

    public function __construct(ConfigAdapterInterface $adapter,
            $default_userspace, StorageInterface $cache = null) {

        $this->cache = $cache;
        $this->adapter = $adapter;
        $this->default_userspace = $userspace;
    }

    public function getConfig($schemadir, $key = "", $userspace = "") {

        $userspace = ($userspace ? $userspace : $this->default_userspace);

        if ($this->cache instanceof StorageInterface) {
            $config = $this->cache->getItem($userspace);

            if (empty($config)) {
                $this->cache->setItem($userspace,
                        $this->adapter->toArray("", $namespace));
            } else {
                $config = $this->cache->getItem($userspace);
            }
        } else {
            $config = $this->adapter->toArray("", $namespace);
        }

        $schema = explode(".", $schemadir);

        $value = &$config;

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

        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $success = $this->adapter->set($schemadir, $key, $value, $value_type,
                $userspace);

        if ($success) {
            if ($this->cache instanceof StorageInterface) {
                $this->cache->setItem($userspace,
                        $this->adapter->toArray("", $userspace));
            }
        }

        return $success;
    }

}
