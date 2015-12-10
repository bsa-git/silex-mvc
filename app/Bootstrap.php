<?php

// app/Bootstrap.php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Translation\Loader as Loader;

/**
 * Class - Bootstrap
 * 
 * @category Bootstrap
 * @package  app\
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
        $app = new Silex\Application();
        $this->app = $app;

        // Create 'stopwatch' object
        $app['watch'] = $app->share(function () {
            return new Stopwatch();
        });
        // Start event 'eApp'
        $app['watch']->start('eApp');


        // Init middlewares
        $this->_iniMiddlewares($app);

        // Init config
        $this->_iniConfig($app);

        // Init providers
        $this->_iniProviders($app);

        // Init services
        $this->_iniServices($app);

        // Extend services
        $this->_extendServices($app);

        // Init controllers
        $this->_iniControllers($app);

        // Init application
        $this->_iniApplication($app);

        //------ WATCH-Bootstrap --------//
        $app['watch']->lap('eApp');
    }

    /**
     *  Init middlewares
     * 
     * @param Application $app 
     */
    private function _iniMiddlewares(Application $app) {

        // The middleware is run before the routing and the security.
        $app->before(function (Request $request, Application $app) {

            // The request body should only be parsed as JSON 
            // if the Content-Type header begins with application/json
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $content = $request->getContent();
                $data = json_decode($content, true);
                $request->request->replace(is_array($data) ? $data : array());
            }
        }, Application::EARLY_EVENT);

        // The middleware is run after the routing and the security.
        $app->before(function (Request $request, Application $app) {

            // Get route
            $attrs = $request->attributes->all();
            if (isset($attrs['_route'])) {
                $route = $attrs['_route'];
                $app['route'] = $route;
            }
            // Get route params
            if (isset($attrs['_route_params']) && count($attrs['_route_params'])) {
                $route_params = $attrs['_route_params'];
                $app['route_params'] = $route_params;
            }
            
        });

        // Set event after the Response
        $app->finish(function (Request $request, Response $response) use ($app) {

            // Stop event 'eApp'
            $event = $app['watch']->stop('eApp');
            if ($app['debug']) {
                $data = array();
                //----------------
                // Get sum profile params
                $duration = $event->getDuration();
                $memory = $event->getMemory();
                $data['Sum'] = array('t' => $duration, 'm' => $memory);
                // Get periods
                $periods = $event->getPeriods();
                // Get profile params for periods
                if (isset($periods[0])) {
                    $bootstrapDuration = $periods[0]->getDuration();
                    $data['Bootstrap'] = array('t' => $bootstrapDuration);
                }
                if (isset($periods[1])) {
                    $routingDuration = $periods[1]->getDuration();
                    $data['Routing'] = array('t' => $routingDuration);
                }
                if (isset($periods[2])) {
                    $controllerDuration = $periods[2]->getDuration();
                    $data['Controller'] = array('t' => $controllerDuration);
                }
                if (isset($periods[3])) {
                    $renderDuration = $periods[3]->getDuration();
                    $data['Render'] = array('t' => $renderDuration);
                }

                $app['monolog']->addDebug('<== Profile:eApp ==>', $data);
            }
        });
    }

    /**
     *  Init config
     * 
     * @param Application $app
     */
    private function _iniConfig(Application $app) {

        // Set type configuration
        $app['yml_config'] = FALSE;

        // Setting the sign of the console
        $app['is_console'] = FALSE;

        // Load configurations
        if ($app['yml_config']) {
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
                    'application.yml', 'security.yml' //  'routing.yml', 'security.yml', 'services.yml'
                ),
                    )
            );
        } else {
            $app['config'] = $app->share(function () use ($app) {
                $params = array();
                //---------------------
                $params['base_path'] = BASEPATH;
                $params['log_path'] = BASEPATH . "/data/logs";
                $params['cache_path'] = BASEPATH . "/data/cache";
                $data = parse_ini_file(BASEPATH . '/app/Resources/Config/app.ini', true);
                foreach ($data['parameters'] as $key => $value) {
                    $params['parameters'][$key] = $this->_formatIniValue($value);
                }
                foreach ($data['controllers'] as $key => $value) {
                    $params['controllers'][$key] = $this->_formatIniValue($value);
                }
                return $params;
            });
        }


        //Set debug option
        $app['debug'] = $app['config']['parameters']['debug'];
        //Set basepath option
        $app['basepath'] = $this->app['config']['base_path'];

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
        if ($app['yml_config']) {
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
        } else {
            // Models
            $app->register(
                    new Providers\ModelsServiceProvider(), array(
                'models.path' => "{$app['basepath']}/app/Models"
            ));

            // Monolog
            $app->register(new Silex\Provider\MonologServiceProvider(), array(
                'monolog.logfile' => "{$app['config']['log_path']}/application.log",
                'monolog.name' => 'APPLICATION'
            ));

            // Twig
            $app->register(
                    new Silex\Provider\TwigServiceProvider(), array(
                'twig.path' => array("{$app['basepath']}/app/Views"),
                'twig.form.templates' => array("Form/bootstrap_3_horizontal_layout.html.twig"),
                'twig.options' => array(
                    'cache' => "{$app['config']['cache_path']}/twig",
                    'strict_variables' => false,
                    'auto_reload' => true)
            ));

            // Translation
            $app->register(
                    new Silex\Provider\TranslationServiceProvider(), array(
                'locale_fallbacks' => array('en', 'ru'),
                'locale' => $app['config']['parameters']['locale']
            ));

            // HttpCache
            $app->register(
                    new Silex\Provider\HttpCacheServiceProvider(), array(
                'http_cache.cache_dir' => "{$app['config']['cache_path']}/http",
                'http_cache.esi' => NULL
            ));

            // SwiftmailerService
            $app->register(
                    new Silex\Provider\SwiftmailerServiceProvider(), array(
                'swiftmailer.options' => array(
                    'host' => $app['config']['parameters']['mail.host'],
                    'port' => $app['config']['parameters']['mail.port'],
                    'username' => $app['config']['parameters']['mail.username'],
                    'password' => $app['config']['parameters']['mail.password'],
                    'encryption' => $app['config']['parameters']['mail.encryption'],
                    'auth_mode' => $app['config']['parameters']['mail.auth_mode']
                )
            ));

            // DoctrineService
            $app->register(
                    new Silex\Provider\DoctrineServiceProvider(), array(
                'dbs.options' => array('default' => array(
                        'driver' => $app['config']['parameters']['db.driver'],
                        'path' => "{$app['basepath']}{$app['config']['parameters']['db.path']}"
                    ))
            ));

            // DoctrineOrmService
            $app->register(
                    new Providers\DoctrineOrmServiceProvider(), array(
                'orm.metadata' => "{$app['basepath']}/app/Models/ORM",
                'orm.options' => array(
                    'driver' => $app['config']['parameters']['db.driver'],
                    'path' => "{$app['basepath']}{$app['config']['parameters']['db.path']}"
                )
            ));

            // ActiveRecord
            $app->register(new Providers\ActiveRecordServiceProvider(), array());

            // Security
            $app->register(
                    new Providers\Security\MySecurityServiceProvider(
                    array(
                'security.role_hierarchy' => array('ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH')),
                'security.access_rules' => array(
                    array('^/admin', 'ROLE_ADMIN'),
                    array('^/test', 'ROLE_ADMIN'),
                    array('^/tasks', 'ROLE_ADMIN'),
                    array('^/account$', 'ROLE_USER'),
                    array('^/blog/new$', 'ROLE_USER'),
                    array('^/todo$', 'ROLE_USER'),
                    array('^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY')
                ),
                'security.firewalls' => array(
                    'default' => array(
                        'pattern' => '^/.*$',
                        'anonymous' => TRUE,
                        'switch_user' => TRUE,
                        'form' => array(
                            'login_path' => '/login',
                            'check_path' => '/login_check',
                            'use_referer' => TRUE),
                        'logout' => '/logout',
//                    'users' => array(
//                        'admin' => array(
//                            'ROLE_ADMIN',
//                            '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
//                        'user' => array(
//                            'ROLE_USER',
//                            '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='))
                    )))), array());



            // ServiceProvider
            $app->register(new Silex\Provider\ServiceControllerServiceProvider(), array());

            // UrlGenerator
            $app->register(new Silex\Provider\UrlGeneratorServiceProvider(), array());

            // SessionService
            $app->register(new Silex\Provider\SessionServiceProvider(), array());

            // FormService
            $app->register(new Silex\Provider\FormServiceProvider(), array());

            // ValidatorService
            $app->register(new Silex\Provider\ValidatorServiceProvider(), array());
        }
    }

    /**
     *  Init services
     *
     * @return void
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
     * 
     * @return void
     */
    private function _extendServices(Application $app) {
        //Extend translator
        if (isset($app['translator']) AND $app['translator'] instanceof \Silex\Translator) {
            $app['translator'] = $app->share(
                    $app->extend(
                            'translator', function (\Silex\Translator $translator, $app) {
                        $translator->addLoader('yml', new Loader\YamlFileLoader());
                        $translator->addResource('yml', BASEPATH . '/app/Resources/translations/messages.en.yml', 'en', 'messages');
                        $translator->addResource('yml', BASEPATH . '/app/Resources/translations/messages.ru.yml', 'ru', 'messages');
                        $translator->addResource('yml', BASEPATH . '/app/Resources/translations/Validators.en.yml', 'en', 'validators');
                        $translator->addResource('yml', BASEPATH . '/app/Resources/translations/Validators.ru.yml', 'ru', 'validators');

                        if ($app['session']->has('_locale')) {
                            $locale = $app['session']->get('_locale');
                            $translator->setLocale($locale);
                        }
                        return $translator;
                    }
                    )
            );
        }

        //Extend Twig
        if (isset($app['twig'])) {
            $app['twig'] = $app->share(
                    $app->extend(
                            'twig', function (Twig_Environment $twig, $app) {
                        $twig->addGlobal('now', time());
                        return $twig;
                    }
                    )
            );

            $app['twig'] = $app->share(
                    $app->extend(
                            'twig', function (Twig_Environment $twig, $app) {
                        $twig->addFunction(
                                new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
                            return sprintf($app['request']->getBasePath() . '/%s', ltrim($asset, '/'));
                        }
                                )
                        );
                        return $twig;
                    }
                    )
            );
        }
    }

    /**
     *  Initialization controllers
     * 
     * @param Application $app
     */
    private function _iniControllers(Application $app) {

        // Error handle
        $app->error(function (\Exception $exc, $code) use ($app) {
            if ($app['debug']) {
                return;
            }

            $exception = FlattenException::create($exc, $code);
            $controller = new Controllers\ErrorController($app);
            return $controller->showAction($app['request'], $exception, null);
        });

//        if ($app['yml_config']) {
//            
//        }  else {
//            
//        }
        // Load controllers
        foreach ($app['config']['controllers'] as $controllerServiceName => $controllerClass) {
            $app[$controllerServiceName] = $app->share(function () use ($app, $controllerClass) {
                return new $controllerClass($app);
            });
            $controller = $app[$controllerServiceName];
        }
    }

    /**
     *  Init application
     * 
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
        $this->app->run();
    }

    
    /**
     *  Format value for ini config file
     * 
     * @param string $value
     * @return string|int|float|bool
     */
    private function _formatIniValue($value) {
        if ($value === 'true' || $value === 'yes') {
            $value = TRUE;
        }
        if ($value === 'false' || $value === 'no') {
            $value = FALSE;
        }
        if ($value === '') {
            $value = NULL;
        }
        if (is_numeric($value)) {
            if (strpos($value, '.') !== false) {
                $value = floatval($value);
            } else {
                $value = intval($value);
            }
        }
        return $value;
    }
}
