<?php

// app/Controllers/Helper/RequestTrait.php

namespace Controllers\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Trait - RequestTrait
 * request operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait RequestTrait
{
    /**
     * Shortcut to return the request service.
     *
     * @return Request
     *
     */
    public function getRequest() {
        return $this->app['request'];
    }
    
    /**
     * Returns true if the request is a XMLHttpRequest
     *
     * It works if your JavaScript library sets an X-Requested-With HTTP header.
     * It is known to work with common JavaScript frameworks:
     *
     * @link http://en.wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     *
     * @return bool true if the request is an XMLHttpRequest, false otherwise
     *
     */
    public function isAjaxRequest() {
        return $this->app['request']->isXmlHttpRequest();
    }
    
    /**
     * Checks if the request method is of specified type.
     *
     * @param string $method Uppercase request method (GET, POST etc).
     *
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->app['request']->isMethod($method);
    }
    
    /**
     * Generates a ABSOLUTE_URL from the given parameters.
     *
     * @param string      $route         The name of the route
     * @param mixed       $parameters    An array of parameters
     * @param bool|string $referenceType The type of reference (ABSOLUTE_PATH, ABSOLUTE_URL)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function url($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_URL) {
        $url = $this->app['url_generator']->generate($route, $parameters, $referenceType);
        return $url;
    }
    
    /**
     * Generates a ABSOLUTE_PATH from the given parameters.
     *
     * @param string      $route         The name of the route
     * @param mixed       $parameters    An array of parameters
     * @param bool|string $referenceType The type of reference (ABSOLUTE_PATH, ABSOLUTE_URL)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function path($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
        $path = $this->app['url_generator']->generate($route, $parameters, $referenceType);
        $path = str_replace('/index.php', '', $path);
        return $path;
    }
    
    /**
     * Forwards the request to another controller.
     *
     * @param array  $url       The URI (/hello)
     * @param array  $query      The query (GET) or request (POST) parameters
     * @param string $method     The HTTP method
     * 
     * @return Response A Response instance
     */
    public function forward($url, array $query = array(), $method = 'GET') {
        $subRequest = Request::create($url, $method, $query);
        return $this->app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Forwards the request to another controller via route
     * 
     *
     * @param array  $route      The route (hello)
     * @param array  $parameters      The $parameters for route
     * @param string $method     The HTTP method
     *
     * @return Response A Response instance 
     */
    public function forwardToRoute($route, array $parameters = array(), $method = 'GET') {
        $url = $this->url($route, $parameters);
        return $this->forward($url, array(), $method);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302) {
        return $this->app->redirect($url, $status);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route      The name of the route
     * @param array  $parameters An array of parameters
     * @param int    $status     The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirectToRoute($route, array $parameters = array(), $status = 302) {
        return $this->redirect($this->path($route, $parameters), $status);
    }

}
