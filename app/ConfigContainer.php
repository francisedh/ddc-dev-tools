<?php

namespace App;

use Config\Loader\YamlConfigLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Description of ConfigContainer
 *
 * @author lbreleur
 */
class ConfigContainer {

    private static $instance = null;

    private function __construct() {
        
    }

    private function __clone() {
        
    }

    public static function getInstance() {
        return (self::$instance instanceof self)? : self::$instance = new self;
    }

    public function retrieve() {
        $loader = new YamlConfigLoader(new FileLocator());
        $data = $loader->load(__DIR__ . '/config/config.yml');

        if (func_num_args() < 1) {
            return $data;
        }

        $current_level = $data;
        for ($i = 0; $i < func_num_args(); $i++) {
            if (isset($current_level[func_get_arg($i)])) {
                $current_level = &$current_level[func_get_arg($i)];
            } else
                return false;
        }
        return $current_level;
    }

}
