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

use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Class Command
 *
 * @category Application
 * @package  Fluency\Silex\Console
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
abstract class Command extends BaseCommand
{
    
    /**
     * Контейнер приложения
     * 
     * @var Silex\Application 
     */
    public $app;
    
    //-----------------------

    /**
     * Constructor 
     * 
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     */
    public function __construct($name = null) {
        parent::__construct($name);
    }
    
    /**
     * Returns the application container.
     *
     * @return \Fluency\Silex\Application
     */
    public function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    /**
     * Returns a service contained in the application container or null if none
     * is found with that name.
     *
     * This is a convenience method used to retrieve an element from the
     * Application container without having to assign the results of the
     * getContainer() method in every call.
     *
     * @param string $name Name of the service
     *
     * @return mixed
     */
    public function getService($name)
    {
        return $this->getContainer()[$name];
    }

    /**
     * Returns parameter from container by name
     *
     * @param string $parameterName The parameter name
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        return $this->getContainer()['config']['parameters'][$parameterName];
    }

    /**
     * Gets the application instance for this command.
     *
     * @return \Fluency\Silex\Console\Application An Application instance
     */
    public function getApplication()
    {
        return parent::getApplication();
    }
}