<?php

// app/Providers/Security/SecurityServiceProvider.php

namespace Providers\Security;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\SecurityServiceProvider;

/**
 * Class - MySecurityServiceProvider
 * Provider for security
 * 
 * @category Provider
 * @package  app\Providers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 * 
 */
class MySecurityServiceProvider implements ServiceProviderInterface {

    private $params;

    public function __construct($params) {
        $this->params = $params;
    }

    public function register(Application $app) {
        if (!isset($this->params['security.firewalls']['default']['users'])) {
            $this->params['security.firewalls']['default']['users'] = $app->share(function () use ($app) {
                return new DbalUserProvider($app['db']);
            });
        }
        $app->register(new SecurityServiceProvider(), $this->params);
    }

    public function boot(Application $app) {
        
    }

}
