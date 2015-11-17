<?php

// app/Controllers/Helper/FlashTrait.php

namespace Controllers\Helper;


/**
 * Trait - FlashTrait
 * flash operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait FlashTrait
{
    
    /**
     * Get session for request
     *
     * @return SessionInterface|null The session 
     */
    public function getRequestSession()
    {
        $request = $this->app['request'];
        $session = $request->getSession();
        return $session;
    }
    
    /**
     * Adds a flash message to the current session for type.
     *
     * @param string $type    The type
     * @param string $message The message
     *
     */
    public function addFlash($type, $message) {
        $this->getRequestSession()->getFlashBag()->add($type, $message);
    }
    
    /**
     * Get flash message and clear for type.
     *
     * @param string $type    The type
     * @param array $default The messages
     *
     * @return string|array 
     */
    public function getFlash($type, array $default = array())
    {
        return $this->getRequestSession()->getFlashBag()->get($type, $default);
    }

    /**
     * Get all flash messages and clear
     *
     *
     * @return string|array 
     */
    public function getFlashes()
    {
        return $this->getRequestSession()->getFlashBag()->all();
    }

    /**
     * Has a flash message for type.
     *
     * @param string $type    The type
     * @return bool 
     */
    public function hasFlash($type)
    {
        return $this->getRequestSession()->getFlashBag()->has($type);
    }
    
    
}
