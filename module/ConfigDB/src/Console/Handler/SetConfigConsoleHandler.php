<?php

namespace ConfigDB\Console\Handler;

use ConfigDB\Service\ConfigDbService;
use ZF\Console\Route;
use ConfigDB\Model\EntryModel;

class SetConfigConsoleHandler {

    protected $configDbService;

    public function __construct(ConfigDbService $configDbService) {
        $this->configDbService = $configDbService;
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
        
        $success = $this->configDbService->setConfig($schemadir, $key, $value, $value_type, $userspace);

        if ($success) {
            $entry = $this->configDbService->getConfig($schemadir, $key, $userspace);
            $console->writeLine(sprintf("schema:%s.%s\tkey:%s\tset as (%s)%s",
                            $userspace, $schemadir, $key, $entry->type, $entry->getValue(true)));
        } else {
            $console->writeLine("Write config failed");
        }
    }

}
