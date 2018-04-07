<?php

namespace ConfigDB\Console\Handler\Factory;

use ConfigDB\Console\Handler\GetConfigConsoleHandler;

class GetConfigConsoleHandlerFactory implements \Zend\ServiceManager\Factory\FactoryInterface {

    public function __invoke(\Interop\Container\ContainerInterface $container,
            $requestedName, array $options = null) {

        
        $configDbService = $container->get(\ConfigDB\Service\ConfigDbService::class);
        
        return new GetConfigConsoleHandler($configDbService);
    }

}
