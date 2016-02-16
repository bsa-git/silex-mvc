<?php

namespace Models\DBAL;

use Doctrine\DBAL\Schema\Schema;

/**
 * Class ConfigurableSchema
 *
 * @category PHPClass
 * @package  Fluency\Silex\Doctrine\DBAL\Schema
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class ConfigurableSchema extends Schema {

    public function createTablesFromConfig($config) {
        try {
            foreach ($config as $tableName => $className) {
                $this->_addTable(new $className($tableName));
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function bundleMultipleSchemas($config) {
        foreach ($config as $alias => $schemaClassName) {
            if (is_string($schemaClassName)) {
                $schema = new $schemaClassName();
                $this->_tables = array_merge($this->getTables(), $schema->getTables());
            }
        }
        return $this;
    }

}
