<?php

namespace ConfigDB\Console\Handler;
use ConfigDB\Adapter\ConfigDatabaseAdapterInterface;

class GetConfigConsoleHandler {

    public function __invoke($route, $console) {
        $this->adapter->toArray();
    }

}
