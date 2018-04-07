<?php

namespace ConfigDB\Adapter\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Db\Adapter\Adapter as ZendDbAdapter;
use Zend\Db\Adapter\AdapterInterface as ZendDbAdapterInterface;
use ConfigDB\Adapter\DatabaseConfigAdapter;

class DatabaseConfigAdapterFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName,
            array $options = null) {

        $moduleOptions = $container->get(\ConfigDB\Options\ModuleOptions::class);

        return new DatabaseConfigAdapter($moduleOptions->getDatabaseAdapter(),
                $moduleOptions->getDatabaseTable(), $moduleOptions->getDefaultUserspace());
    }

}
