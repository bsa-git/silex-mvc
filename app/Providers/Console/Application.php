<?php
/**
 * PHP version ~5.5
 *
 * @category Application
 * @package  Fluency\Silex\Console
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */

namespace Providers\Console;


use Symfony\Component\Console\Application as ConsoleApplication;
use Silex\Application as SilexApplication;

/**
 * Class Application
 *
 * @category Application
 * @package  Fluency\Silex\Console
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class Application extends ConsoleApplication
{
    protected $container;

    /**
     * Constructor
     *
     * @param SilexApplication $app     Silex application instance
     * @param string           $name    The name of the application
     * @param string           $version The version of the application
     */
    public function __construct(SilexApplication $app,
        $name = 'Fluency\Silex', $version = '1.0.0'
    ) {
        parent::__construct($name, $version);

        $this->container = $app;
        $app->boot();
    }

    /**
     * Gets current container instance
     *
     * @return SilexApplication
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets service from container
     *
     * @param string $serviceName The service name
     *
     * @return mixed
     */
    public function getService($serviceName)
    {
        return $this->container[$serviceName];
    }

    /**
     * Gets parameter from container by name
     *
     * @param string $parameterName The parameter name
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        return $this->container['config']['parameters'][$parameterName];
    }
}