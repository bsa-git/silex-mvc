<?php

// app/Providers/QuickForm2ServiceProvider.php

namespace Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;

//use Models\UbkiData;

/**
 * Class - QuickForm2ServiceProvider
 * Provider for form
 * 
 * @category Provider
 * @package  app\Providers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 * 
 */
class QuickForm2ServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        // HTML_QuickForm2
        set_include_path(get_include_path() . PATH_SEPARATOR . BASEPATH . "/library/QuickForm2");
        require_once BASEPATH . '/library/QuickForm2/HTML/QuickForm2.php';
        require_once BASEPATH . '/library/QuickForm2/HTML/QuickForm2/Renderer.php';
    }

    public function boot(Application $app) {
        
    }

}
