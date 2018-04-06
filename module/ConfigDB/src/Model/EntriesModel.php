<?php

namespace ConfigDB\Model;

use ConfigDB\Model\EntryModel;

class EntriesModel extends \ArrayObject {
    public function __construct() {
        return false;
    }

    public function addEntry(EntryModel $entry) {
        $this->offsetSet($entry->name, $entry);
    }
}
