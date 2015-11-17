<?php

// app/Providers/ModelsServiceProvider.php

namespace Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class - ModelsServiceProvider
 * Provider for models
 * 
 * @category Provider
 * @package  app\Providers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 * 
 */
class ModelsServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app['models.path'] = array();
        $app['models'] = $app->share(function($app) {
            return new GetModels($app);
        });
    }

    public function boot(Application $app) {
        
    }

}

class GetModels {

    private $app;

    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function load($modelName, $modelMethod = null, $data = array()) {
        if (isset($this->app["models.{$modelName}"])) {
            $Model = $this->app["models.{$modelName}"];
        } else {
            $modelClass = "\\Models\\{$modelName}Model";
            $Model = new $modelClass();
            $Model->setApp($this->app);
            $this->app["models.{$modelName}"] = $Model;
        }
        return $Model->$modelMethod($data);
    }

}
