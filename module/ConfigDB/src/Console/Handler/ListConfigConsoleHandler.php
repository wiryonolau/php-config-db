<?php

namespace ConfigDB\Console\Handler;
use ConfigDB\Adapter\ConfigDatabaseAdapterInterface;

class ListConfigConsoleHandler {

    protected $adapter;

    public function __construct(ConfigDatabaseAdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function __invoke($route, $console) {
        print_r($this->adapter->toArray());
    }

}
