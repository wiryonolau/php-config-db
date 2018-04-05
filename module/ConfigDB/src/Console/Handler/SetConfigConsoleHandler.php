<?php

namespace ConfigDB\Console\Handler;

use ConfigDB\Adapter\ConfigDatabaseAdapterInterface;
use ZF\Console\Route;

class SetConfigConsoleHandler {

    protected $adapter;

    public function __construct(ConfigDatabaseAdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function __invoke(Route $route, $console) {
        $schemadir = $route->getMatchedParam("schemadir", false);
        $userspace = $route->getMatchedParam("userspace", "");
        $key = $route->getMatchedParam("key", false);
        $value_type = $route->getMatchedParam("valuetype", "string");
        $value = $route->getMatchedParam("value", false);

        if (!($schemadir or $key or $value)) {
            $console->writeLine("Invalid format");
            return 1;
        }

        
        $success = $this->adapter->set($schemadir, $key, $value, $value_type, $userspace);

        if ($success) {
            $value = $this->adapter->get($schemadir, $key, $userspace);
            $console->writeLine(sprintf("schema:%s.%s key:%s set as %s",
                            $userspace, $schemadir, $key, $value));
        } else {
            $console->writeLine("Write config failed");
        }
    }

}
