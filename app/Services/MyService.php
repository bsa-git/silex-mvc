<?php

// app/Services/SysBox.php

namespace Services;

use Silex\Application;

/**
 * Class - MyService
 * Add my service
 *
 * @category Service
 * @package  app\Services
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class MyService {

    /**
     * Container application
     * 
     * @var Application 
     */
    protected $app;

    //---------------------
    /**
     * Constructor
     *  
     * @param Application $app
     */
    public function __construct(Application $app) {

        $this->app = $app;

        // Set debug service
        $this->getDebug();
    }

    //========= PRINT FUNC ==========

    /**
     * Get service for ZF
     * 
     * @param mixed $service
     * @return mixed
     */
    public function get($service) {

        $method = 'get';
        $service = strtolower($service);
        $arService = explode('_', $service);
        foreach ($arService as $item) {
            $method .= ucfirst($item);
        }
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            $this->app->abort(404, "Service {$service} not Found");
        }
    }

    /**
     * Get config service
     * 
     * @return Config
     */
    public function getConfig() {
        $app = $this->app;
        //-----------------
        // Config
        if (!isset($app['my.config'])) {
            $app['my.config'] = $app->share(function ($app) {
                return new \Config\Config($app);
            });
        }
        return $app['my.config'];
    }

    /**
     * Get system service
     * 
     * @return System
     */
    public function getSystem() {
        $app = $this->app;
        //-----------------
        // System
        if (!isset($app['my.system'])) {
            $app['my.system'] = $app->share(function ($app) {
                return new \Services\My\System($app);
            });
        }
        return $app['my.system'];
    }

    /**
     * Get string service
     * 
     * @return String
     */
    public function getString() {
        $app = $this->app;
        //-----------------
        // Sys
        if (!isset($app['my.string'])) {
            $app['my.string'] = $app->share(function ($app) {
                return new \Services\My\String();
            });
        }
        return $app['my.string'];
    }

    /**
     * Get http service
     * 
     * @return Http
     */
    public function getHttp() {
        $app = $this->app;
        //-----------------
        // Http
        if (!isset($app['my.http'])) {
            $app['my.http'] = function () {
                return new \Services\My\Http();
            };
        }
        return $app['my.http'];
    }

    /**
     * Get xml service
     * 
     * @return Xml
     */
    public function getXml() {
        $app = $this->app;
        //-----------------
        // Xml
        if (!isset($app['my.xml'])) {
            $app['my.xml'] = function () {
                return new \Services\My\CrXml();
            };
        }
        return $app['my.xml'];
    }

    /**
     * Get array service
     * 
     * @return ArrayBox
     */
    public function getArray() {
        $app = $this->app;
        //-----------------
        // ArrayBox
        if (!isset($app['my.array'])) {
            $app['my.array'] = $app->share(function () {
                return new \Services\My\ArrayBox();
            });
        }
        return $app['my.array'];
    }

    /**
     * Get param service
     * 
     * @return ParamBox
     */
    public function getParam() {
        $app = $this->app;
        //-----------------
        // ParamBox
        if (!isset($app['my.param'])) {
            $app['my.param'] = $app->share(function () {
                return new \Services\My\ParamBox();
            });
        }
        return $app['my.param'];
    }

    /**
     * Get debug service
     * 
     * @return ParamBox
     */
    public function getDebug() {
        $app = $this->app;
        //-----------------
        // ParamBox
        if (!isset($app['my.debug'])) {
            $app['my.debug'] = $app->share(function () {
                return new \Services\My\ParamBox();
            });
        }
        return $app['my.debug'];
    }
    
    
    /**
     * Get Markdown service
     * Markdown is a lightweight and easy-to-use syntax for text styling
     * according to http://daringfireball.net/projects/markdown/syntax
     * 
     * @return Parsedown
     */
    public function getMarkdown() {
        $app = $this->app;
        //-----------------
        // ParamBox
        if (!isset($app['my.markdown'])) {
            $app['my.markdown'] = $app->share(function () {
                return new \Services\My\markdown\Markdown();
            });
        }
        return $app['my.markdown'];
    }
    
    /**
     * Get MarkdownGithub service
     * Markdown is a lightweight and easy-to-use syntax for text styling
     * according to https://help.github.com/articles/github-flavored-markdown
     * 
     * @return Parsedown
     */
    public function getMarkdownGithub() {
        $app = $this->app;
        //-----------------
        // ParamBox
        if (!isset($app['my.markdown_github'])) {
            $app['my.markdown_github'] = $app->share(function () {
                return new \Services\My\markdown\GithubMarkdown();
            });
        }
        return $app['my.markdown_github'];
    }
    
    /**
     * Get MarkdownExtra service
     * Markdown is a lightweight and easy-to-use syntax for text styling
     * according to http://michelf.ca/projects/php-markdown/extra
     * 
     * @return Parsedown
     */
    public function getMarkdownExtra() {
        $app = $this->app;
        //-----------------
        // ParamBox
        if (!isset($app['my.markdown_extra'])) {
            $app['my.markdown_extra'] = $app->share(function () {
                return new \Services\My\markdown\MarkdownExtra();
            });
        }
        return $app['my.markdown_extra'];
    }
}
