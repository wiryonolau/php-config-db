<?php

namespace ConfigDB\Console\Handler;
use ConfigDB\Service\ConfigDbService;

class ListConfigConsoleHandler {

    protected $configDbService;

    public function __construct(ConfigDbService $configDbService) {
        $this->configDbService = $configDbService;
    }

    public function __invoke($route, $console) {
        $schemadir = $route->getMatchedParam("schemadir", "");

        $entries = $this->configDbService->getConfig($schemadir);

        print_r($entries->getArrayCopy());
        
        return 0;
        
    }

}
