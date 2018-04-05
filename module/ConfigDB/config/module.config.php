<?php

namespace ConfigDB;

use Zend\ServiceManager\Factory\InvokableFactory;

return array(
    'service_manager' => array(
        'factories' => array(
            Console\Handler\GetConfigConsoleHandler::class => InvokableFactory::class,
            Console\Handler\SetConfigConsoleHandler::class => Console\Handler\Factory\SetConfigConsoleHandlerFactory::class,
            Console\Handler\ListConfigConsoleHandler::class => Console\Handler\Factory\ListConfigConsoleHandlerFactory::class,
            Adapter\FileDatabaseAdapter::class => Adapter\Factory\FileDatabaseAdapterFactory::class
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                array(
                    'name' => 'get',
                    'route' => ' [--userspace=] <schemadir> <key>',
                    'short_description' => 'Get Config',
                    'handler' => Console\Handler\GetConfigConsoleHandler::class,
                    'defaults' => array(
                        'userspace' => 'global'
                    )
                ),
                array(
                    'name' => 'set',
                    'route' => ' [--userspace=] [--valuetype=] <schemadir> <key> <value>',
                    'short_description' => 'Set Config',
                    'handler' => Console\Handler\SetConfigConsoleHandler::class,
                    'defaults' => array(
                        'userspace' => 'global'
                    )
                ),
                array(
                    'name' => 'list-key',
                    'route' => ' [--userspace=] [<schemadir>]',
                    'short_description' => 'List Config',
                    'handler' => Console\Handler\ListConfigConsoleHandler::class,
                    'defaults' => array(
                        'userspace' => 'global'
                    )
                )
            )
        ),
    ),
);


