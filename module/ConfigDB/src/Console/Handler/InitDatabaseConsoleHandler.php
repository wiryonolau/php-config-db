<?php

namespace ConfigDB\Console\Handler;

use ConfigDB\Service\InitDbService;

class InitDatabaseConsoleHandler {
    
    protected $initDbService;
    
    public function __construct(InitDbService $initDbService) {
        $this->initDbService = $initDbService;
    }
    
    public function __invoke($route, $console) {
        $this->initDbService->initDatabase();
    }
}