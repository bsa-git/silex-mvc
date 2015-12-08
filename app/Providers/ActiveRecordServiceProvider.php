<?php

// app/Providers/ActiveRecordServiceProvider.php

namespace Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;

//use Models\UbkiData;

/**
 * Class - ActiveRecordServiceProvider
 * Provider for models
 * 
 * @category Provider
 * @package  app\Providers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 * 
 */
class ActiveRecordServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        // PHPActiveRecord 'sqlite' => 'sqlite://my_database.db',
        require_once BASEPATH . '/library/AR/ActiveRecord.php';
        $app['ar'] = $app->share(function ($app) {
            // Obtaining the orm manager
            return new \Models\AR\Db($app);
        });
    }

    public function boot(Application $app) {
        
    }

}