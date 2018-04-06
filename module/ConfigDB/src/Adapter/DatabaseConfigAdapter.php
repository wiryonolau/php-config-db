<?php

namespace ConfigDB\Adapter;

use ConfigDB\Adapter\ConfigAdapterInterface;
use ConfigDB\Model\EntryModel;
use Zend\Db\Adapter\Adapter as ZendDbAdapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Hydrator\ArraySerializable;

class DatabaseConfigAdapter implements ConfigAdapterInterface {

    protected $adapter;
    protected $tablename;
    protected $default_userspace;

    public function __construct(ZendDbAdapter $adapter, $tablename,
            $default_userspace = "global") {

        $this->adapter = $adapter;
        $this->tablename = $tablename;
        $this->default_userspace = $default_userspace;
    }

    public function set($schemadir, $key, $value,
            $value_type = EntryModel::TYPE_STRING) {

        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $entryModel = new EntryModel($key, $value, $value_type);

        $sql = new Sql($this->adapter);
        $insert = $sql->insert($this->tablename);
        $insert->columns(["userspace", "schema", "key", "type", "value"]);
        $insert->values([$userspace, $schemadir, $entryModel->key, $entryModel->type, $entryModel->getValue(true)]);

        $statement = $sql->prepareStatementForSqlObject($insert);
        $statement->execute();
    }

    public function get($schemadir, $key, $userspace = "") {
        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $rows = [];

        $sql = new Sql($this->adapter);

        $select = $sql->select();
        $select->from($this->tablename);
        $select->where(array(
            'userspace' => $userspace,
            'schema' => $schemadir,
            'key' => $key
        ));

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result instanceof ResultInterface) {
            $rowset = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);

            $resultSet = new ResultSet();
            $resultSet->initialize($rowset);

            $hydrator = new ArraySerializable;

            foreach ($resultSet->getDataSource() as $row) {
                $rows[] = $hydrator->hydrate($row, new EntryModel());
            }
        }

        return $rows;
    }

    public function listKeys($schemadir = "", $userspace = "") {
        
    }

    public function toArray($schemadir = "", $userspace = "") {
        
    }

}
