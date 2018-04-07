<?php

namespace ConfigDB;

use Zend\ServiceManager\Factory\InvokableFactory;

return array(
    'service_manager' => array(
        'factories' => array(
            Console\Handler\GetConfigConsoleHandler::class => Console\Handler\Factory\GetConfigConsoleHandlerFactory::class,
            Console\Handler\SetConfigConsoleHandler::class => Console\Handler\Factory\SetConfigConsoleHandlerFactory::class,
            Console\Handler\ListConfigConsoleHandler::class => Console\Handler\Factory\ListConfigConsoleHandlerFactory::class,
            Console\Handler\InitDatabaseConsoleHandler::class => Console\Handler\Factory\InitDatabaseConsoleHandlerFactory::class,
            Options\ModuleOptions::class => Options\Factory\ModuleOptionsFactory::class,
            Adapter\FileConfigAdapter::class => Adapter\Factory\FileConfigAdapterFactory::class,
            Adapter\DatabaseConfigAdapter::class => Adapter\Factory\DatabaseConfigAdapterFactory::class,
            Service\InitDbService::class => Service\Factory\InitDbServiceFactory::class,
            Service\ConfigDbService::class => Service\Factory\ConfigDbServiceFactory::class
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                array(
                    'name' => 'get',
                    'route' => '[--userspace=] <schemadir> <key>',
                    'short_description' => 'Get Config',
                    'handler' => Console\Handler\GetConfigConsoleHandler::class,
                    'defaults' => array(
                        'userspace' => 'global'
                    )
                ),
                array(
                    'name' => 'set',
                    'route' => '[--userspace=] [--valuetype=] <schemadir> <key> <value>',
                    'short_description' => 'Set Config',
                    'handler' => Console\Handler\SetConfigConsoleHandler::class,
                    'defaults' => array(
                        'userspace' => 'global'
                    )
                ),
                array(
                    'name' => 'list',
                    'route' => '[--userspace=] <schemadir>',
                    'short_description' => 'List Config',
                    'handler' => Console\Handler\ListConfigConsoleHandler::class,
                    'defaults' => array(
                        'userspace' => 'global'
                    )
                ),
                array(
                    'name' => 'initdb',
                    'short_description' => 'Init config using Database',
                    'handler' => Console\Handler\InitDatabaseConsoleHandler::class,
                )
            )
        ),
    ),
);


