<?php

// app/Providers/DoctrineOrmServiceProvider.php

namespace Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Class - DoctrineOrmServiceProvider
 * Setup Doctrine ORM
 * 
 * @category Provider
 * @package  app\Providers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 * 
 */
class DoctrineOrmServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {

        $app['em'] = $app->share(function ($app) {

            // Create a simple "default" Doctrine ORM configuration for Annotations
            $isDevMode = true;
            $config = Setup::createAnnotationMetadataConfiguration(array($app['orm.metadata']), $isDevMode);

            // Obtaining the entity manager
            return EntityManager::create($app['orm.options'], $config);
        });
    }

    public function boot(Application $app) {
        
    }

}
