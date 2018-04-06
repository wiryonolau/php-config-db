<?php

namespace ConfigDB;

return [
    "configdb" => [
        "database_adapter" => Adapter\FileConfigAdapter::class,
        "default_userspace" => "global"
    ],
];
