<?php

namespace ConfigDB;

return [
    "configdb" => [
        /*
         * Config Adapter to use
         * ConfigDB\Adapter\FileConfigAdapter - File base config
         * ConfigDB\Adapter\DatabaseConfigAdapter - Database config
         */
        "config_adapter" => Adapter\FileConfigAdapter::class,
        /*
         * Database Adapter if use ConfigDb\Adapter\DatabaseAdapter
         * Instance of Zend\Db\Adapter\Adapter
         * Database Adapter Config Array
         */
        "database_adapter" => [
            'driver' => 'Pdo_Mysql',
            /*Docker mysql*/
            'hostname' => 'php-config-db-mysql',
            'port' => 3306,
            'database' => 'configdb',
            'username' => 'configdb',
            'password' => '888888',
            'driver_options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => true,
                \PDO::MYSQL_ATTR_LOCAL_INFILE => true
            ]
        ],
        "database_table" => "configdb",
        /*
         * Cache storage
         * Instance of cache storage interface
         * Cache adapter config
         */
        "cache_config" => false, 
        "cache_adapter" => [
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'dir_level' => 1,
                    'cache_dir' => 'data/cache',
                    'namespace' => 'configdb',
                    'ttl' => 300
                )
            ),
            'plugins' => array(
                'serializer',
                'exception_handler' => array(
                    'throw_exceptions' => true
                )
            )
        ],
        /*
         * 
         */
        "default_userspace" => "global"
    ],
];
