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
        $this->default_userspace = $default_userspace;
    }

    public function listKey($schemadir = "", $userspace = "") {
        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $config = $this->getCache($userspace);

        $value = $this->findSchema($schemadir, $config);

        return $this->findKey($value);
    }

    public function getConfig($schemadir, $key = "", $userspace = "") {

        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $config = $this->getCache($userspace);

        $value = $this->findSchema($schemadir, $config);

        if (!isset($value[EntriesModel::ENTRIES_KEY])) {
            throw new \Exception("Schema doesn't have entry");
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

    private function findKey(& $someArray) {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($someArray),
                \RecursiveIteratorIterator::SELF_FIRST);

        $keys = [];

        foreach ($iterator as $k => $v) {
            //$indent = str_repeat(' ;', 10 * $iterator->getDepth());
            // Not at end: show key only
            if ($k == "__entries") {
                for ($p = array(), $i = 0, $z = $iterator->getDepth() - 1; $i <= $z; $i++) {
                    $p[] = $iterator->getSubIterator($i)->key();
                }
                $path = implode('.', $p);

                foreach (array_keys($v->getArrayCopy()) as $entry) {
                    if ($path) {
                        $keys[] = sprintf("%s:%s", $path, $entry);
                    } else {
                        $keys[] = $entry;
                    }
                }
            }
        }

        return $keys;
    }

    private function findSchema($schemadir, &$value) {
        if (!empty($schemadir)) {
            $schema = explode(".", $schemadir);

            foreach ($schema as $path) {
                if (empty($value[$path])) {
                    throw new \Exception("Schema not exist");
                }

                $value = $value[$path];
            }
        }

        return $value;
    }

    private function getCache($userspace) {
        if ($this->cache instanceof StorageInterface) {
            $config = $this->cache->getItem($userspace);

            if (empty($config)) {
                $this->cache->setItem($userspace,
                        $this->adapter->toArray("", $userspace));

                $config = $this->cache->getItem($userspace);
            }
        } else {
            $config = $this->adapter->toArray("", $userspace);
        }

        return $config;
    }

}
