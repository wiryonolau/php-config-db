<?php

namespace ConfigDB\Adapter\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ConfigDB\Adapter\FileDatabaseAdapter;

class FileDatabaseAdapterFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null) {

        $default_userspace = $container->get("config")["configdb"]["default_userspace"];

        return new FileDatabaseAdapter($default_userspace);
    }

}
