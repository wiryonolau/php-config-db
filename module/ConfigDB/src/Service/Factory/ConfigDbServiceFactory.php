<?php

namespace ConfigDB\Service\Factory;

use ConfigDB\Service\ConfigDbService;

class ConfigDbServiceFactory implements \Zend\ServiceManager\Factory\FactoryInterface {

    public function __invoke(\Interop\Container\ContainerInterface $container,
            $requestedName, array $options = null) {

        $moduleOptions = $container->get(\ConfigDB\Options\ModuleOptions::class);

        return new ConfigDbService($moduleOptions->getConfigAdapter($container),
                $moduleOptions->getDefaultUserspace(),
                $moduleOptions->getCacheAdapter());
    }

}
