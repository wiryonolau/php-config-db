<?php

namespace ConfigDB\Console\Handler;
use ConfigDB\Adapter\ConfigAdapterInterface;

class ListConfigConsoleHandler {

    protected $adapter;

    public function __construct(ConfigAdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function __invoke($route, $console) {
        $schemadir = $route->getMatchedParam("schemadir", "");

        $entries = $this->adapter->toArray($schemadir);
        print_r($entries);
    }

}
