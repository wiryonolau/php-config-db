# PHP Configuration Database 

Manage application configuraion inside database, each configuration are separate by userspace.

Configuration can be save in File / Database such as MySQL.

Required ZF3

## Installation

```bash
composer require wiryonolau/php-config-db
```

## Test using Docker
```bash
#Start php-cli docker container
make start 

#Build composer dependency
docker exec -it php-config-db composer-update
docker exec -it php-config-db gosu 1000 bin/configdb
 
#If you want to use mysql as backend run this
make start-mysql
make connect
docker exec -it php-config-db gosu 1000 bin/configdb initdb
```

## Usage
Specify configuration in your application with key "configdb"

Config Parameters :
 - config_adapter:
   - ConfigDB\Adapter\FileConfigAdapter : Configuration is save inside folder
   - ConfigDB\Adapter\DatabaseConfigAdapter : Configruation is save inside specified database
   - Any class that implement ConfigDB\Adapter\ConfigAdapterInterface
 - database_adapter :
   - An array of database configuration - check Zend\Db\Adapter\Adapter for format
   - Any class that implement Zend\Db\Adapter\AdapterInterface
 - database_table : table name to save configuration database
 - cache_config : (bool) whether to cache config
 - cache_adapter : cacheing adapter
   - An array of cache configuration 
   - Any class that implement Zend\Cache\Storage\StorageInterface
 - default_userspace : default userspace to use , default is "global"

### Example : 
MyModule\config\configdb.config.php
```php
<?php
return [
    "configdb" => [
        "config_adapter" => \ConfigDB\Adapter\DatabaseConfigAdapter::class,
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
        "default_userspace" => "global"
    ],
];

```
MyModule\src\Controller\Factory\MyClassFactory.php
```php
namespace MyModule\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MyClassFactory implements FactoryInterface {
    public function __invoke($container, $requestedName, $options) {
        $configDbService = $container->get(\ConfigDB\Service\ConfigDbService::class);
        return new MyClass($configDbService);
    }
}
```
MyModule\src\Controller\MyClass.php
```php
namespace MyModule\Controller;

class MyClass {
    public function __construct($configDbService) {
        #to get config
        $configDbService->getConfig($shcemadir, $key, $userspace);
        
        #to set config
        $configDbService->setConfig($schemadir, $key, $value, $value_type, $userspace)
    }
}
```

### Returned Value

All Value is return either as ConfigDB\Model\EntryModel or ConfigDB\Model\EntriesModel