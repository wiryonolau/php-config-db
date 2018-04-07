<?php

namespace ConfigDB\Service\Factory;

use ConfigDB\Service\InitDbService;
use Zend\Db\Adapter\Adapter as ZendDbAdapter;

class InitDbServiceFactory implements \Zend\ServiceManager\Factory\FactoryInterface {

    public function __invoke(\Interop\Container\ContainerInterface $container,
            $requestedName, array $options = null) {

        $moduleOptions = $container->get(\ConfigDB\Options\ModuleOptions::class);

        return new InitDbService($moduleOptions->getDatabaseAdapter());
    }

}
