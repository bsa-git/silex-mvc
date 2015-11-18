<?php

// app/Bootstrap.php

use Silex\Application;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Translation\Loader as Loader;

/**
 * Class - Bootstrap
 * 
 * @category Bootstrap
 * @package  app\Console
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class Bootstrap {

    /**
     * Application
     * 
     * @var Silex\Application 
     */
    protected $app;

    //----------------
    /**
     * Constructor 
     * 
     */
    public function __construct() {


        require_once BASEPATH . '/vendor/autoload.php';

        // Create app
        $this->app = new Silex\Application();

        // Init config
        $this->_iniConfig($this->app);

        // Init providers
        $this->_iniProviders($this->app);

        // Init services
        $this->_iniServices($this->app);

        // Extend services
        $this->_extendServices($this->app);

        // Init commands
        $this->_iniCommands($this->app);

        // Init application
        $this->_iniApplication($this->app);
    }

    /**
     *  Init config
     * 
     * @param Application $app
     */
    private function _iniConfig(Application $app) {

        // Setting the sign of the console
        $app['is_console'] = TRUE;

        // Load configurations
        $app->register(
                new Providers\YamlConfigServiceProvider(
                array(
            '%base_path%' => BASEPATH,
            '%log_path%' => BASEPATH . '/data/logs',
            '%cache_path%' => BASEPATH . '/data/cache'
                )
                ), array(
            'config.dir' => BASEPATH . '/app/Resources/Config',
            'config.files' => array(
                'console.yml'//, 'services.yml'
            ),
                )
        );

        //Set debug option
        $app['debug'] = $app['config']['parameters']['debug'];

        //Set Timezone
        if (isset($app['config']['parameters']['timezone'])) {
            date_default_timezone_set($app['config']['parameters']['timezone']);
        }

        // Registering the ErrorHandler
        // It converts all errors to exceptions, and exceptions are then caught by Silex
        ErrorHandler::register();
        ExceptionHandler::register($app['debug']);
    }

    /**
     *  Init providers
     * 
     * @param Application $app
     */
    private function _iniProviders(Application $app) {

        // Register the service providers
        foreach ($app['config']['service_providers'] as $serviceProviderConfig) {
            $app->register(
                    new $serviceProviderConfig['class'](
                    (!isset($serviceProviderConfig['construct_parameters'])) ?
                            null : $serviceProviderConfig['construct_parameters']
                    ), (isset($serviceProviderConfig['parameters']) &&
                    null !== $serviceProviderConfig['parameters']) ?
                            $serviceProviderConfig['parameters'] : array()
            );
        }
    }

    //========= SERVICES ============

    /**
     * Init services
     *
     * @param Application $app
     */
    protected function _iniServices(Application $app) {

        // MyService
        $app['my'] = $app->share(function ($app) {
            return new \Services\MyService($app);
        });
        
        // ZendService
        $app['zend'] = $app->share(function ($app) {
            return new \Services\ZendService($app);
        });
    }

    /**
     *  Extend services
     * 
     * @param Application $app
     */
    private function _extendServices(Application $app) {
        //Extend translator
        if (isset($app['translator']) AND $app['translator'] instanceof \Silex\Translator) {
            $app['translator'] = $app->share(
                    $this->app->extend(
                            'translator', function (\Silex\Translator $translator, $app) {
                        $translator->addLoader('yaml', new Loader\YamlFileLoader());
                        $translator->addResource('yaml', BASEPATH . '/app/Resources/translations/messages.en.yaml', 'en', 'messages');
                        $translator->addResource('yaml', BASEPATH . '/app/Resources/translations/messages.ru.yaml', 'ru', 'messages');
                        $translator->addResource('yaml', BASEPATH . '/app/Resources/translations/Validators.en.yaml', 'en', 'validators');
                        $translator->addResource('yaml', BASEPATH . '/app/Resources/translations/Validators.ru.yaml', 'ru', 'validators');
                        return $translator;
                    }
                    )
            );
        }
    }

    /**
     *  Init commands
     * 
     * @param Application $app
     */
    private function _iniCommands(Application $app) {

        foreach ($app['config']['commands'] as $name => $commandConfig) {
            $classCommand = $commandConfig['class'];
            if (isset($commandConfig['configure'])) {
                $commandName = $commandConfig['configure']['name'];
                $commandInstance = new $classCommand($commandName);
                if (property_exists($commandInstance, 'app')) {
                    $commandInstance->app = $app;
                }
                if (property_exists($commandInstance, 'config_name')) {
                    $commandInstance->config_name = $name;
                }

                $commandInstance->setCommandConfig();
            } else {
                $commandInstance = new $classCommand();
                if (property_exists($commandInstance, 'app')) {
                    $commandInstance->app = $app;
                }
            }
            $app['console']->add($commandInstance);
        }
    }

    /**
     * Init application
     * @param Application $app
     */
    private function _iniApplication(Application $app) {
        // Create application paths
        $config = $app['my']->get('config')->createAppPaths();
    }

    /**
     *  Run this application
     */
    public function run() {

        // Run app
        $this->app['console']->run();
    }

}
