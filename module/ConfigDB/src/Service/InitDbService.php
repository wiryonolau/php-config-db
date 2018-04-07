<?php

namespace ConfigDB\Service;

use Zend\Db\Adapter\Adapter as ZendDbAdapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Ddl;
use Zend\Db\Sql\Ddl\Column;
use Zend\Db\Sql\Ddl\Constraint;

class InitDbService {

    protected $adapter;

    public function __construct(ZendDbAdapter $adapter) {
        $this->adapter = $adapter;
    }

    public function initDatabase() {
        $adapter = $this->adapter;
        
        $table = new Ddl\CreateTable('configdb');

        $table->addColumn(new Column\Varchar('userspace', 255));
        $table->addColumn(new Column\Varchar('schemadir', 255));
        $table->addColumn(new Column\Varchar('key', 255));
        $table->addColumn(new Column\Varchar('type', 255));
        $table->addColumn(new Column\Varchar('value', 255));

        $table->addConstraint(
                new Constraint\UniqueKey(['userspace', 'schemadir', 'key'],
                'config_unique')
        );

        $sql = new Sql($adapter);

        $adapter->query(
                $sql->getSqlStringForSqlObject($table),
                $adapter::QUERY_MODE_EXECUTE
        );
    }

}
