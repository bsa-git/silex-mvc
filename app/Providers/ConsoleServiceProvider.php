<?php

// app/Providers/ConsoleServiceProvider.php

namespace Providers;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Providers\Console\Application as ConsoleApplication;
use Providers\Console\EventListener\LoggerListener;
use Silex\Application as SilexApplication;
use Silex\ServiceProviderInterface;

/**
 * Class ConsoleServiceProvider
 *
 * @category ServiceProvider
 * @package  app\Providers
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class ConsoleServiceProvider implements ServiceProviderInterface {

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param SilexApplication $app Silex application instance
     *
     * @return ConsoleApplication
     */
    public function register(SilexApplication $app) {
        $app['console'] = $app->share(
                function () use ($app) {
            $application = new ConsoleApplication(
                    $app, $app['console.name'], $app['console.version']
            );

            // Sets default connection helper for DBAL
            if (isset($app['db'])) {
                $application->getHelperSet()->set(
                        new ConnectionHelper($app['db']), 'db'
                );
            }
            
            // Sets default connection helper for ORM
            if (isset($app['em'])) {
                $application->getHelperSet()->set(
                        new EntityManagerHelper($app['em']), 'em'
                );
            }

            $application->setDispatcher($app['dispatcher']);

            return $application;
        }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param SilexApplication $app Application instance
     *
     * @return void
     */
    public function boot(SilexApplication $app) {
        $consoleDispatcher = $app['dispatcher'];
        $consoleDispatcher->addSubscriber(new LoggerListener);
        $app['dispatcher'] = $consoleDispatcher;
    }

}
