<?php

namespace ConfigDB\Console\Handler\Factory;

use ConfigDB\Console\Handler\SetConfigConsoleHandler;

class SetConfigConsoleHandlerFactory implements \Zend\ServiceManager\Factory\FactoryInterface {

    public function __invoke(\Interop\Container\ContainerInterface $container,
            $requestedName, array $options = null) {

        $adapter = $container->get("config")["configdb"]["database_adapter"];
        $adapter = $container->get($adapter);
        
        return new SetConfigConsoleHandler($adapter);
    }

}
