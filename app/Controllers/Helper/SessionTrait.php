<?php

// app/Controllers/Helper/SessionTrait.php

namespace Controllers\Helper;

use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Trait - SessionTrait
 * session operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait SessionTrait
{
    /**
     * Get user session
     *
     * @return object|NULL
     */
    public function getUserSession() {
        if (!isset($this->app['session'])) {
            throw new \LogicException('The \"SessionServiceProvider\" is not registered in your application.');
        }

        $user = $this->app['session']->get('user');
        return $user;
    }
    
    /**
     * Get user session
     * 
     * @param array $values ex. array('username' => $username)
     * @return object|NULL
     */
    public function setUserSession($values = array()) {
        if (!isset($this->app['session'])) {
            throw new \LogicException('The \"SessionServiceProvider\" is not registered in your application.');
        }
        
        // Get old values
        $values_ = $this->getUserSession();
        // Set new values
        foreach ($values as $key => $value) {
            if(isset($values_[$key]) && is_null($value)){
                unset($values_[$key]);
                continue;
            }
            $values_[$key] = $value;
        }
        
        $this->app['session']->set('user', $values_);
        
        return $this->getUserSession();
    }
    
    /**
     * Get session
     *
     * @return Session
     */
    public function getSession() {
        if (!isset($this->app['session'])) {
            throw new \LogicException('The \"SessionServiceProvider\" is not registered in your application.');
        }

        return $this->app['session'];;
    }
    
    /**
     * Get session value
     *
     * @param string $key
     * @return Misc
     */
    public function getSessValue($key) {
        $session = $this->getSession();

        return $session->get($key);
    }
    
    /**
     * Has session value
     *
     * @param string $key
     * @return bool
     */
    public function hasSessValue($key) {
        $session = $this->getSession();

        return $session->has($key);
    }
    
    /**
     * Set session value
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setSessValue($key, $value) {
        $session = $this->getSession();

        $session->set($key, $value);
    }
}
