<?php

// app/Models/AR/Model.php

namespace Models\AR;

use Silex\Application;

/**
 * Class - Model 
 * Base model
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class Model extends \ActiveRecord\Model {

    use \Controllers\Helper\PaginationTrait;
    
    /**
     * Name column with a primary key
     * @var string
     */
    static $primary_key = 'id';

    //-------------------------

    /**
     * Container application
     * 
     * @var Application 
     */
    public static $app_static;

    /**
     * Container application
     * 
     * @var Application 
     */
    protected $app;

    //--------------------------

    /**
     * Get Application
     *
     * @return Application
     */
    public static function getAppStatic() {
        return self::$app_static;
    }

    /**
     * Set Application
     *
     */
    public static function setAppStatic(Application $app) {
        self::$app_static = $app;
    }

    /**
     * Set Application
     *
     * @param Application $app
     *
     * @return Todo
     */
    public function setApp(Application $app) {
        self::setAppStatic($app);
        $this->app = $app;
        return $this;
    }

    /**
     * Get Application
     *
     * @return Application
     */
    public function getApp() {
        return $this->app;
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool true if the service id is defined, false otherwise
     */
    public function has($id) {
        if (!isset($this->app)) {
            return FALSE;
        }
        return isset($this->app[$id]);
    }

    /**
     * Gets a container service by its id.
     *
     * @param string $id The service id
     *
     * @return object|NULL The service
     */
    public function get($id) {
        if ($this->has($id)) {
            return $this->app[$id];
        } else {
            return NULL;
        }
    }

    /**
     * Set object values
     *
     * @param  array $aValues
     * @return Entity_XXXX
     */
    public function setValues(array $aValues) {
        foreach ($aValues as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Get object values
     *
     * @return array
     */
    public function getValues() {
        $arrResult = array();
        //----------------------
        $attributes = $this->attributes();
        foreach ($attributes as $key => $value) {
            $arrResult[$key] = $this->$key;
        }
        return $arrResult;
    }
    
    /**
     * Save the model to the database.
     *
     * @param  boolean $validate Set to true or false depending on if you want the validators to run or not
     * @return array
     */
    public function saveModel($validate = true) {
        if (!$this->save($validate)) {
            $strErr = implode("<br>\n", $this->errors->full_messages());
            $this->app->abort(405, "Failed to save record \"{$strErr}\"");
        }
    }
}
