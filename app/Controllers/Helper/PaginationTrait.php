<?php

// app/Controllers/Helper/PaginationTrait.php

namespace Controllers\Helper;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter as Adapter;
use Pagerfanta\View as View;
//use Pagerfanta\View\Template as Template;

/**
 * Trait - PaginationTrait
 * pagination operations
 *
 * @category Helper
 * @package  app\Controllers\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
trait PaginationTrait {

    /**
     * Create adapter for page pagination
     *
     * @param string $class  The adapter class
     * @param string $model  The model class
     * @param array $opts  
     * 
     * @return mix Adapter object
     */
    public function createPaginatorAdapter($class, $model, $opts = array()) {
        $adapter = new $class($model, $opts);
        return $adapter;
    }

    /**
     * Create paginator
     *
     * @param AdapterInterface|null $adapter  The adapter object
     * @param array $params 
     * @return Pagerfanta
     */
    public function createPaginator(Adapter\AdapterInterface $adapter, $params = array()) {
        if (count($params)) {
            if (isset($params['opts'])) {
                $adapter = $this->createAdapter($params['class'], $params['model'], $params['opts']);
            } else {
                $adapter = $this->createAdapter($params['class'], $params['model']);
            }
        }
        $paginator = new Pagerfanta($adapter);
        $paginator->setMaxPerPage($this->app['config']['parameters']['pagerfanta.max_per_page']);
        return $paginator;
    }

    /**
     * Render view for page pagination
     *
     * @param PagerfantaInterface $pagerfanta  The Pagerfanta object
     * @param function $routeGenerator  The function
     * @param array $opts  
     * @return string
     */
    public function renderPaginator($pagerfanta, $routeGenerator, $params = array()) {
        $app = $this->app;
        $params_default = array(
            'proximity' => $app['config']['parameters']['pagerfanta.proximity'],
            'prev_message' => "<span class=\"glyphicon glyphicon-chevron-left\"></span> {$app['translator']->trans('previous')}", 
            'next_message' => "{$app['translator']->trans('next')} <span class=\"glyphicon glyphicon-chevron-right\"></span>"
        );
        $params = array_replace($params_default, $params);    
        //-----------------
        $view = new View\TwitterBootstrap3View();
        $html = $view->render($pagerfanta, $routeGenerator, $params);
        return $html;
    }

}
