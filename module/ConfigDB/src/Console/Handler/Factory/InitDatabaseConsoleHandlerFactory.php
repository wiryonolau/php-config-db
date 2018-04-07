<?php

namespace ConfigDB\Console\Handler\Factory;

use ConfigDB\Console\Handler\InitDatabaseConsoleHandler;

class InitDatabaseConsoleHandlerFactory implements \Zend\ServiceManager\Factory\FactoryInterface {

    public function __invoke(\Interop\Container\ContainerInterface $container,
            $requestedName, array $options = null) {

        $service = $container->get(\ConfigDB\Service\InitDbService::class);
        
        return new InitDatabaseConsoleHandler($service);
    }

}
