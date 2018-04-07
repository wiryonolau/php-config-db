<?php

namespace ConfigDB\Adapter;

use ConfigDB\Adapter\ConfigAdapterInterface;
use ConfigDB\Model\EntryModel;
use ConfigDB\Model\EntriesModel;

class FileConfigAdapter implements ConfigAdapterInterface {

    const CONFIG_FILE = "entry.json";

    protected $default_userspace;

    public function __construct($default_userspace) {
        $this->default_userspace = $default_userspace;
    }

    public function get($schemadir, $key, $userspace = "") {

        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $file = implode(DIRECTORY_SEPARATOR,
                [$this->constructPath($schemadir, $userspace), self::CONFIG_FILE]);

        if (file_exists($file)) {

            $content = json_decode(file_get_contents($file), true);
            $index = array_search($key, array_column($content, "name"));

            $entry = new EntryModel($content[$index]["name"],
                    $content[$index]["value"], $content[$index]["type"]);

            return $entry;
        }

        throw new \Exception("Entry not exist");
    }

    public function set($schemadir, $key, $value,
            $value_type = EntryModel::TYPE_STRING, $userspace = "") {

        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $path = self::constructPath($schemadir, $userspace);

        @mkdir($path, 0770, true);

        if (is_dir($path)) {

            $entry = $path . DIRECTORY_SEPARATOR . self::CONFIG_FILE;

            $entryModel = new EntryModel($key, $value, $value_type);
            $value_arr = $entryModel->toArray();

            $content[] = $value_arr;

            if (file_exists($entry)) {
                $content = json_decode(file_get_contents($entry), true);
                $index = array_search($key, array_column($content, "name"));

                if ($index !== false) {
                    $content[$index] = $value_arr;
                } else {
                    $content[] = $value_arr;
                }
            }

            file_put_contents($entry, json_encode($content, JSON_PRETTY_PRINT));
            chmod($entry, 0770);

            return true;
        }
        return false;
    }

    public function toArray($schemadir = "", $userspace = "") {
        $userspace = ($userspace ? $userspace : $this->default_userspace);

        $path = $this->constructPath($schemadir, $userspace);
        $dir = $this->directoryIteratorToArray(new \DirectoryIterator($path));
        return $dir;
    }

    private function directoryIteratorToArray(\DirectoryIterator $it) {
        $result = array();
        foreach ($it as $key => $child) {
            if ($child->isDot()) {
                continue;
            }
            $name = $child->getBasename();
            if ($child->isDir()) {
                $subit = new \DirectoryIterator($child->getPathname());
                $result[$name] = $this->directoryIteratorToArray($subit);
            } elseif ($child->isFile() === true and $child->getFilename() === self::CONFIG_FILE) {
                $content = file_get_contents($child->getPath() . DIRECTORY_SEPARATOR . $child->getBasename());
                $entries = json_decode($content, true);
                
                $entriesModel = new EntriesModel();
                foreach ($entries as $entry) {
                    try {
                        $entry = new EntryModel($entry["name"], $entry["value"],
                                $entry["type"]);
                        $entriesModel->addEntry($entry);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                $result["__entries"] = $entriesModel;
            } else {
                $result[] = $name;
            }
        }
        return $result;
    }

    private function constructPath($schemadir, $userspace = "") {
        $root_dir = [APP_PATH, "data", "configdb", $userspace];

        $dirs = explode(".", $schemadir);

        foreach ($dirs as $dir) {
            $root_dir[] = $dir;
        }

        return implode(DIRECTORY_SEPARATOR, $root_dir);
    }

}
