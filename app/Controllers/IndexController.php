<?php

// app/Controllers/IndexController.php

namespace Controllers;

use Silex\Application;
use Symfony\Component\Yaml\Yaml;

/**
 * Class - IndexController
 * 
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class IndexController extends BaseController {
    //-----------------------------

    /**
     * Construct
     * 
     * @param Application $app
     */
    public function __construct(Application $app) {
        parent::__construct($app);
    }

    /**
     * Routes initialization
     * 
     * @return void
     */
    protected function iniRoutes() {
        $self = $this;
        $this->app->get('/', function () use ($self) {
            return $self->indexAction();
        })->bind('default');
        $this->app->get('/home', function () use ($self) {
            return $self->indexAction();
        })->bind('home');
        $this->app->get('/locale/{lang}', function ($lang) use ($self) {
            return $self->localeAction($lang);
        })->bind('locale');
        $this->app->get('/scheme/{scheme}', function ($scheme) use ($self) {
            return $self->schemeAction($scheme);
        })->bind('scheme');
        $this->app->post('/lang', function () use ($self) {
            return $self->langAction();
        })->bind('lang');
    }

    /**
     * Action - index/index
     * 
     * @return string
     */
    public function indexAction() {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            return $this->showView();
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - index/locale
     * set locale
     * 
     * @param string $lang (en,ru,uk)
     * @return string
     */
    public function localeAction($lang) {

        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            $this->setSessValue('_locale', $lang);
            if ($this->hasSessValue('prev_url')) {
                $url = $this->getSessValue('prev_url');
                return $this->redirect($url);
            } else {
                return $this->redirect("/home");
            }
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }
    
    /**
     * Action - index/scheme
     * set scheme
     * 
     * @param string $scheme (en,ru,uk)
     * @return string
     */
    public function schemeAction($scheme) {

        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            $this->setSessValue('_scheme', $scheme);
            if ($this->hasSessValue('prev_url')) {
                $url = $this->getSessValue('prev_url');
                return $this->redirect($url);
            } else {
                return $this->redirect("/home");
            }
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - index/lang
     * loading data localization
     *
     * @return void
     */
    public function langAction() {
        $data = array();
        $hash = "";
        //--------------
        try {

            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            if ($this->isAjaxRequest()) {
                
                // get hash for translation messages
                $hash = $this->getLangHash();
                
                if(isset($this->params['hash']) && $this->params['hash'] !== $hash){
                    $arTrans = $this->getLangMsgs();
                    $data['hash'] = $hash;
                    $data['values'] = $arTrans;
                }
                return $this->sendJson($data);
            }
        } catch (\Exception $exc) {
            return $this->errorAjax($exc);
        }
    }

}
