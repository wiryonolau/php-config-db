<?php

namespace ConfigDB\Options\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ConfigDB\Options\ModuleOptions;

class ModuleOptionsFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null) {

        $module_config = $container->get('config')['configdb'];

        return new ModuleOptions($module_config);
    }

}
