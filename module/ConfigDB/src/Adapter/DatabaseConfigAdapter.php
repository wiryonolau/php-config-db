<?php

namespace ConfigDB\Adapter;

use ConfigDB\Adapter\ConfigAdapterInterface;
use ConfigDB\Model\EntryModel;
use ConfigDB\Model\EntriesModel;
use Zend\Db\Adapter\Adapter as ZendDbAdapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;

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
            $value_type = EntryModel::TYPE_STRING, $userspace = "") {

        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $entryModel = new EntryModel($key, $value, $value_type);


        $sql = new Sql($this->adapter);

        $insert = $sql->insert($this->tablename);

        $insert->values([
            "userspace" => $userspace,
            "schemadir" => $schemadir,
            "key" => $entryModel->name,
            "type" => $entryModel->type,
            "value" => $entryModel->getValue(true)
        ]);

        $insert_stmt = $sql->prepareStatementForSqlObject($insert);

        $update = $sql->update($this->tablename);

        $update->set([
            "type" => $entryModel->type,
            "value" => $entryModel->getValue(true)
        ]);

        $update->where([
            "userspace" => $userspace,
            "schemadir" => $schemadir,
            "key" => $entryModel->name
        ]);

        $update_stmt = $sql->prepareStatementForSqlObject($update);

        try {
            $insert_stmt->execute();
        } catch (\Exception $e) {
            echo $e->getMessage(), "\n";
            try {
                $update_stmt->execute();
            } catch (\Exception $e) {
                echo $e->getMessage(), "\n";
                return false;
            }
        }

        return true;
    }

    public function get($schemadir, $key, $userspace = "") {
        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $rows = new \ArrayIterator();

        $sql = new Sql($this->adapter);

        $select = $sql->select();
        $select->from($this->tablename);
        $select->where(array(
            'userspace' => $userspace,
            'schemadir' => $schemadir,
            'key' => $key
        ));

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();

        if ($result instanceof ResultInterface) {
            $rowset = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);

            $resultSet = new ResultSet();
            $resultSet->initialize($rowset);

            $rows = $this->convertResult($resultSet->getDataSource());
        }

        return $rows;
    }

    public function toArray($schemadir = "", $userspace = "") {
        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $rows = new \ArrayIterator();

        $sql = new Sql($this->adapter);

        $select = $sql->select();
        $select->from($this->tablename);

        $where = [];

        if ($userspace) {
            $where["userspace"] = $userspace;
        }

        if ($schemadir) {
            $where["schemadir"] = $schemadir;
        }

        $select->where($where);

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();

        if ($result instanceof ResultInterface) {
            $rowset = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);

            $resultSet = new ResultSet();
            $resultSet->initialize($rowset);

            $rows = $this->convertResult($resultSet->getDataSource());
        }

        return $rows;
    }

    private function convertResult($rows) {

        $config = [];

        foreach ($rows as $row) {

            $entry = new EntryModel($row["key"], $row["value"], $row["type"]);

            $arr = $this->convertSchemaToArray($row["schemadir"], $config,
                    $entry);
        }
        
        return $config;
    }

    private function convertSchemaToArray($schemadir, &$config, $entry) {
        $temp = &$config;
        foreach (explode(".", $schemadir) as $key) {
            $temp = &$temp[$key];
        }

        if (!isset($temp[EntriesModel::ENTRIES_KEY])) {
            $temp[EntriesModel::ENTRIES_KEY] = new EntriesModel();
        }

        $temp[EntriesModel::ENTRIES_KEY]->addEntry($entry);

        return $temp;
    }

}
