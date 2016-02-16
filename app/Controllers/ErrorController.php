<?php

// app/Controllers/ErrorController.php

namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * Class - ErrorController
 * Error handling
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class ErrorController extends BaseController {
    //-----------------------------

    /**
     * @var bool Show error (false) or exception (true) pages by default.
     */
    protected $debug;
    protected $twig;

    //------------------------------
    /**
     * Construct 
     * 
     * @param Application $app
     */
    public function __construct(Application $app) {
        parent::__construct($app);

        $this->twig = $app['twig'];
        $this->debug = $app['debug'];
    }

    /**
     * Converts an Exception to a Response.
     *
     * A "showException" request parameter can be used to force display of an error page (when set to false) or
     * the exception page (when true). If it is not present, the "debug" value passed into the constructor will
     * be used.
     *
     * @param Request              $request   The request
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null) {
        $opts = array();
        $params = array();
        //------------------------------
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC

        $code = $exception->getStatusCode();
        $message = $exception->getMessage();
        $arrAlertMessage = $this->getAlertMessage($message);
        $attrs = $request->attributes->all();
        if(isset($attrs['_route'])){
            $route = $attrs['_route'];
        }  else {
            $route = 'indefinite';
        }
        
        if (isset($this->app["my.opts"]) && isset($this->app["my.opts"])) {
            $opts = $this->app["my.opts"];
            $params = $this->app["my.params"];
        }
        

        return new Response($this->twig->render(
                        $this->findTemplate($request, $request->getRequestFormat(), $code, $showException), array(
                    'route' => $route,
                    'status_code' => $code,
                    'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception' => $exception,
                    'logger' => $logger,
                    'currentContent' => $currentContent,
                    'parameters' => $this->ArrData2View($opts + $params)
                        ) 
        ));
    }

    /**
     * @param int $startObLevel
     *
     * @return string
     */
    protected function getAndCleanOutputBuffering($startObLevel) {
        if (ob_get_level() <= $startObLevel) {
            return '';
        }

        Response::closeOutputBuffers($startObLevel + 1, true);

        return ob_get_clean();
    }

    /**
     * @param Request $request
     * @param string  $format
     * @param int     $code          An HTTP response status code
     * @param bool    $showException
     *
     * @return TemplateReferenceInterface
     */
    protected function findTemplate(Request $request, $format, $code, $showException) {
        $name = $showException ? 'exception' : 'error';
        if ($showException && 'html' == $format) {
            $name = 'exception_full';
        }

        // For error pages, try to find a template for the specific HTTP status code and format
        if (!$showException) {
            $template = $this->getPath($name . $code, $format, 'twig');
            if ($this->templateExists($template)) {
                return $template;
            }
        }

        // try to find a template for the given format
        $template = $this->getPath($name, $format, 'twig');
        if ($this->templateExists($template)) {
            return $template;
        }

        // default to a generic HTML exception
        $request->setRequestFormat('html');

        return $this->getPath($showException ? 'exception_full' : $name, 'html', 'twig');
    }

    // to be removed when the minimum required version of Twig is >= 2.0
    protected function templateExists($template) {
        $loader = $this->twig->getLoader();
        if ($loader instanceof \Twig_ExistsLoaderInterface) {
            return $loader->exists($template);
        }

        try {
            $loader->getSource($template);

            return true;
        } catch (\Twig_Error_Loader $e) {
            
        }

        return false;
    }

    /**
     * Returns the path to the template
     *  - as a path when the template is not part of a bundle
     *  - as a resource when the template is part of a bundle.
     * 
     * @param string $name (exception_full, exception, error)
     * @param string $format (html, json, js ...) 
     * @param string $engine (twig...)
     * @return string A path to the template or a resource
     */
    protected function getPath($name, $format, $engine) {
        $path = "Controller/error/" . $name . '.' . $format . '.' . $engine;
        return $path;
    }

    /**
     * Get an array of nested array of strings to display
     * 
     * @param array $arData 
     * @param bool $isTitle
     * @return array
     *  
     */
    protected function ArrData2View($arData = array(), $isTitle = TRUE) {
        $results = array();
        //----------------------
        foreach ($arData as $key => $value) {
            $keyIsString = is_string($key);
            if (is_array($value)) {
                if (!count($value)) {
                    continue;
                }
                if ($isTitle && $keyIsString) {
                    $key = strtoupper($key);
                    $results[] = "-----{$key}-----";
                }
                foreach ($value as $key_ => $value_) {
                    if (!is_string($value_)) {
                        $value_ = var_export($value_, TRUE);
                    }
                    $keyIsString = is_string($key_);
                    $value_ = str_replace("\"", "'", $value_);
                    if ($keyIsString) {
                        $results[] = "{$key_} = \"{$value_}\"";
                    } else {
                        $results[] = "{$value_}";
                    }
                }
            } else {
                if (!is_string($value)) {
                    $value = var_export($value, TRUE);
                }
                $value = str_replace("\"", "'", $value);
                if ($keyIsString) {
                    $results[] = "{$key} = \"{$value}\"";
                } else {
                    $results[] = "{$value}";
                }
            }
        }
        return $results;
    }

}
