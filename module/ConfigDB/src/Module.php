<?php

namespace ConfigDB;

class Module {

    public function getConfig() {
        $config = array();

        $configFiles = array(
            include __DIR__ . '/../config/module.config.php',
            include __DIR__ . '/../config/configdb.config.php',
        );

        foreach ($configFiles as $file) {
            $config = \Zend\Stdlib\ArrayUtils::merge($config, $file);
        }

        return $config;
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}
