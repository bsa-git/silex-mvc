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
        \ActiveRecord\Config::initialize(function($cfg) use ($app) {
            $cfg->set_model_directory(BASEPATH . '/app/Models');
            $cfg->set_connections(array('production' => "sqlite://../app/Resources/db/simple.db"));
            $cfg->set_default_connection('production');
        });
    }

    public function boot(Application $app) {
        
    }

}