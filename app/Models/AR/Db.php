<?php

// app/Models/AR/Orm.php

namespace Models\AR;

use Silex\Application;

/**
 * Class - Db
 * to initialize and configure AR
 *
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class Db {

    /**
     * Container application
     * 
     * @var Application 
     */
    protected $app;

    /**
     * Check the minimum version of PHP, connect the necessary classes, 
     * registration autoloader models, initialization configuration
     *
     * @param Application $app
     * 
     */
    function __construct(Application $app) {

        $this->app = $app;

        if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300) {
            die('PHP ActiveRecord requires PHP 5.3 or higher');
        }
        // Initialize AR
        $this->_initialize($app);
    }

    /**
     * Initialize AR
     * 
     * @return void
     */
    private function _initialize(Application $app) {
        $dbPath = $app['basepath'] . $app['config']['parameters']['db.models.path'];
        $dbConnectionProduction = $app['config']['parameters']['db.connection.production'];
        $dbConnectionDevelopment = $app['config']['parameters']['db.connection.development'];
        $dbConnectionTest = $app['config']['parameters']['db.connection.test'];
        //-----------------------
        // Initialize AR
        $cfg = \ActiveRecord\Config::instance();
        $cfg->set_model_directory($dbPath);
        $cfg->set_connections(array(
            'production' => $dbConnectionProduction,
            'development' => $dbConnectionDevelopment,
            'test' => $dbConnectionTest
        ));
        $cfg->set_date_format("Y-m-d H:i:s");
        $cfg->set_default_connection($app['config']['parameters']['environment']);
    }

    /**
     * Get entity class
     * 
     * @param string $modelName
     * @return string
     */
    public function getClass($modelName) {
        // Returns a string with the first character of str capitalized
        $modelName = ucfirst($modelName);
        $class = "\\{$this->app['config']['parameters']['db.models.namespace']}\\{$modelName}";
        if (!class_exists($class)) {
            $this->app->abort(404, "Not declared class \"{$class}\"");
        }
        return $class;
    }

    /**
     * Get model object
     * 
     * @param string $modelName
     * @param array $attributes
     * @return ActiveRecord\Model
     */
    public function create($modelName, $attributes = array()) {
        $class = $this->getClass($modelName);

        if (count($attributes)) {
            $model = new $class;
            foreach ($attributes as $key => $value) {
                $model->$key = $value;
            }
            $model->setApp($this->app);
            return $model;
        } else {
            $model = new $class;
            $model->setApp($this->app);
            return $model;
        }
    }

    /**
     * Find entitys
     * 
     * @param string $modelName
     * @param int|string|null $arg1 Else $arg1=null then ( e.g. Book::find(array(2,3)))
     * @param array $opts
     * @return array|ActiveRecord\Model
     */
    public function find($modelName, $arg1, $opts = array()) {
        $class = $this->getClass($modelName);

        if ($arg1 === NULL && count($opts)) {
            $models = $class::find($opts);
        } else {
            if (count($opts)) {
                $models = $class::find($arg1, $opts);
            } else {
                $models = $class::find($arg1);
            }
        }

        // Set app for model
        if (is_array($models)) {
            $arrModels = array();
            foreach ($models as $model) {
                $model->setApp($this->app);
                $arrModels[] = $model;
            }
            return $arrModels;
        } else {
            $models->setApp($this->app);
            return $models;
        }
    }

    /**
     * Determine if a record exists.
     * 
     * @param string $modelName
     * @param int|array $opts
     * @return boolean
     */
    public function exists($modelName, $opts) {
        $class = $this->getClass($modelName);
        return $class::exists($opts);
    }

    /**
     * Get/Set attributes
     * 
     * @param string $modelName
     * @param int $id
     * @param array $attributes
     * @return array|ActiveRecord\Model
     */
    public function attributes($modelName, $id, $attributes = array()) {
        $arrAttributes = array();
        //-------------------
        $model = $this->find($modelName, $id);
        if (count($attributes)) {
            foreach ($attributes as $key => $value) {
                $model->$key = $value;
            }
            return $model;
        } else {
            $attributes = $model->attributes();
            foreach ($attributes as $key => $value) {
                $arrAttributes[$key] = $model->$key;
            }
            return $arrAttributes;
        }
    }

    /**
     * Get data for request (sql) only read
     * 
     * @param string $modelName
     * @param string $sql
     * @return array
     */
    public function sql($modelName, $sql) {
        // Returns a string with the first character of str capitalized
        $class = $this->getClass($modelName);
        $sql = $class::connection()->escape($sql);
        return $class::find_by_sql($sql);
    }

    /**
     * Get SQLBuilder
     * 
     * @param string $tableName
     * @param string $connection_name
     * @return SQLBuilder
     */
    public function sqlBuilder($tableName, $connection_name = NULL) {
        $connection = \ActiveRecord\ConnectionManager::get_connection($connection_name);
        $sqlBuilder = new ActiveRecord\SQLBuilder($connection, $tableName);
        return $sqlBuilder;
    }

    /**
     * Get last query
     * 
     * @return string
     */
    public function lastQuery() {
        $connection = \ActiveRecord\ConnectionManager::get_connection();
        $sql = $connection->last_query;
        return $sql;
    }

    /**
     * Get last query
     * 
     * @return string
     */
    public function lastInsertId() {
        $connection = \ActiveRecord\ConnectionManager::get_connection();
        $id = $connection->insert_id();
        return $id;
    }

}
