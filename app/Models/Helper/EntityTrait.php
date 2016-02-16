<?php

// app/Models/Helper/DbalTrait.php

namespace Models\Helper;

use Silex\Application;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Trait - EntityTrait
 * Entity operations
 *
 * @category Helper
 * @package  app\Models\Helper
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
trait EntityTrait {

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
     * Set object resolve values
     *
     * @param  array $aValues
     * @return Entity_XXXX
     */
    public function setResolveValues(array $aValues) {

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $values = $resolver->resolve($aValues);

        $this->setValues($values);

        return $this;
    }

    /**
     * Set object values
     *
     * @param  array $aValues
     * @return Entity_XXXX
     */
    public function setValues(array $aValues) {
        foreach ($aValues as $key => $value) {
            if ($key[0] == '_' || $key == 'app') {
                continue;
            }
            $method = 'set';
            $key = strtolower($key);
            $arKey = explode('_', $key);
            foreach ($arKey as $item) {
                $method .= ucfirst($item);
            }
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
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
        // Get object values
        $objectVars = get_object_vars($this);
        foreach ($objectVars as $key => $val) {
            if ($key[0] == '_' || $key == 'app') {
                continue;
            }
            $method = 'get';
            $arKey = explode('_', $key);
            foreach ($arKey as $item) {
                $method .= ucfirst($item);
            }
            if (method_exists($this, $method)) {
                $value = $this->$method();
                if ($value !== NULL && !is_object($value)) {
                    $arrResult[$key] = $value;
                }
            }
        }
        return $arrResult;
    }

}
