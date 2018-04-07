<?php

namespace ConfigDB\Model;

class EntryModel {

    const TYPE_BOOL = "bool";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_STRING = "string";
    const TYPE_LIST = "list";

    protected $name;
    protected $type;
    protected $value;

    public function __construct($name, $value, $value_type = self::TYPE_STRING) {
        $this->setName($name);
        $this->setValue($value, $value_type);
    }

    public function __set($key, $value) {
        switch ($key) {
            case "key":
            case "name":
                $this->setName($value);
                break;
            case "value":
                $this->setValue($value);
                break;
            default:
        }
    }

    public function __get($key) {
        if (!in_array($key, ["name", "type", "value"])) {
            throw new \Exception(sprintf("%s : %s", get_class($this),
                    "Parameters not exist"));
        }

        switch ($key) {
            case "value":
                return $this->getValue();
            default:
                return $this->{$key};
        }
    }

    public function getValue($value_as_string = false) {
        if (!$value_as_string) {
            return $this->value;
        }

        if (is_array($this->value)) {
            $value = json_encode($this->value);
        } else {
            $value = (string) $this->value;
        }

        return $value;
    }

    public function setName($name) {
        if (!$name) {
            throw new \Exception("Entry name empty");
        }

        if (!preg_match('/^[a-z0-9_.]+$/', $name)) {
            throw new \Exception(sprintf("%s : %s", get_class($this),
                    "Invalid config name format given"));
        }

        $this->name = $name;
    }

    public function setValue($value, $value_type = self::TYPE_STRING) {
        switch ($value_type) {
            case self::TYPE_BOOL:
            case self::TYPE_BOOLEAN:
                if (!is_string($value)) {
                    $value = (bool) $value;
                    break;
                }
                switch (strtolower($value)) {
                    case '1':
                    case 'true':
                    case 'on':
                    case 'yes':
                    case 'y':
                        $value = true;
                        break;
                    default:
                        $value = false;
                }
                break;
            case self::TYPE_LIST:
                if (is_string($value)) {
                    $values = explode(",", $value);
                    $value = array_map("trim", $values);
                }
                break;
            case self::TYPE_STRING:
                $value = (string) $value;
                break;
            default:
                throw new \Exception(sprintf("%s : %s", get_class($this),
                        "Invalid value type given"));
        }

        $this->type = $value_type;
        $this->value = $value;
    }

    public function isValid() {
        if ($this->name and ! is_null($this->value)) {
            return true;
        }
        return false;
    }

    public function toArray() {
        if (!$this->isValid()) {
            throw new \Exception(sprintf("%s : %s", get_class($this),
                    "Invalid entry"));
        }

        return [
            "name" => $this->name,
            "type" => $this->type,
            "value" => $this->value
        ];
    }

}
