<?php

namespace ConfigDB\Console\Handler;
use ConfigDB\Adapter\ConfigAdapterInterface;

class ListConfigConsoleHandler {

    protected $adapter;

    public function __construct(ConfigAdapterInterface $adapter) {
        $this->adapter = $adapter;
    }

    public function __invoke($route, $console) {
        print_r($this->adapter->toArray());
    }

}
