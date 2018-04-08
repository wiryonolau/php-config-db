<?php

namespace ConfigDB\Console\Handler;

use ConfigDB\Service\ConfigDbService;

class ListKeyConsoleHandler {

    protected $configDbService;

    public function __construct(ConfigDbService $configDbService) {
        $this->configDbService = $configDbService;
    }

    public function __invoke($route, $console) {
        $schemadir = $route->getMatchedParam("schemadir", "");

        $entries = $this->configDbService->listKey($schemadir);
        
        foreach($entries as $entry) {
            $console->writeLine($entry);
        }

        return 0;
    }

}
