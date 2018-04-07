<?php

namespace ConfigDB\Adapter\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ConfigDB\Adapter\FileConfigAdapter;

class FileConfigAdapterFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null) {

        $moduleOptions = $container->get(\ConfigDB\Options\ModuleOptions::class);

        return new FileConfigAdapter($moduleOptions->getDefaultUserspace());
    }

}
