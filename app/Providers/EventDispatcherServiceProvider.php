<?php

// app/Providers/ConsoleServiceProvider.php

namespace Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Registers EventDispatcher and related services with the Pimple Container. By
 * defaults dispatcher exists on Silex application, this provides a clean instance
 *
 * @category ServiceProvider
 * @package  app\Providers
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 * @api
 */
class EventDispatcherServiceProvider implements ServiceProviderInterface {

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app Application instance
     *
     * @return void
     */
    public function register(Application $app) {
        $app['dispatcher'] = function () {
            return new EventDispatcher;
        };
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app Application instance
     *
     * @return void
     */
    public function boot(Application $app) {
        
    }

}
