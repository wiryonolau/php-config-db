<?php

namespace ConfigDB\Model;

use ConfigDB\Model\EntryModel;

class EntriesModel extends \ArrayObject {

    const ENTRIES_KEY = "__entries";
    
    public function addEntry(EntryModel $entry) {
        $this->offsetSet($entry->name, $entry);
    }
}
