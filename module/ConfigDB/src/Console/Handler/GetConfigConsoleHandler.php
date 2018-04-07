<?php

namespace ConfigDB\Console\Handler;

use ConfigDB\Service\ConfigDbService;

class GetConfigConsoleHandler {

    protected $configDbService;

    public function __construct(ConfigDbService $configDbService) {
        $this->configDbService = $configDbService;
    }

    public function __invoke($route, $console) {
       
        $schemadir = $route->getMatchedParam("schemadir", false);
        $userspace = $route->getMatchedParam("userspace", "");
        $key = $route->getMatchedParam("key", false);
 
        if ($schemadir and $key) {
            $entry = $this->configDbService->getConfig($schemadir, $key, $userspace);
            $console->writeLine(sprintf("schema:%s.%s\tkey:%s\tvalue:(%s)%s",
                            $userspace, $schemadir, $key, $entry->type, $entry->getValue(true)));
            return 0;
        } 
        
        throw new \Exception("Please specify schemadir and config key");
        
 
    }

}
