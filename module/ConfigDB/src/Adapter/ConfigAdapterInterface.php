<?php

namespace ConfigDB\Adapter;

use ConfigDB\Model\EntryModel;

interface ConfigDatabaseAdapterInterface {

    public function set($schemadir, $key, $value, $value_type = EntryModel::TYPE_STRING,
            $userspace = "");

    public function get($schemadir, $key, $userspace = "");
    public function listKeys($schemadir = "", $userspace="");
    public function toArray($schemadir = "", $userspace = "");

}
