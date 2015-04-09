<?php

namespace Config\Loader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Parser;

/**
 * Description of YamlConfigLoader
 *
 * @author .
 */
class YamlConfigLoader extends FileLoader {

    public function load($resource, $type = null) {

        $parser = new Parser();
        $configValues = $parser->parse(file_get_contents($resource));
        $this->importAll($configValues, $resource);

        return $configValues;
    }

    public function supports($resource, $type = null) {
        return is_string($resource) && 'yml' === pathinfo(
                        $resource, PATHINFO_EXTENSION
        );
    }

    private function importAll(&$configValues, $parentResource) {
        if (!isset($configValues['imports']) || !is_array($configValues['imports'])) {
            return;
        }

        foreach ($configValues['imports'] as $import) {
            $array = explode("/", $parentResource);
            $config = $this->import($resource = str_replace(end($array), $import['resource'], $parentResource));

            if (is_array($config) && $import['resource'] == 'parameters.yml') {
                $this->loadParameters($configValues, $config['parameters'], $import['resource'], $parentResource);
            } else if (is_array($config)) {
                $configValues = array_merge($configValues + $config, $configValues);
            }
        }

        unset($configValues['imports']);
    }

    private function loadParameters(&$configValues, $params, $resource, $parentResource) {
        \array_walk_recursive($configValues, function(&$value) use ($params, $resource, $parentResource) {
            preg_match("/\%[a-z|_|0-9|.]*\%/", $value, $matches);

            foreach ($matches as $match) {
                if (!isset($params[$paramKey = str_replace('%', '', $match)])) {
                    throw new \Exception("Parameter $value is requested in $parentResource but not found in $resource");
                }

                $value = str_replace($match, $params[$paramKey], $value);
            }
        });
    }

}
