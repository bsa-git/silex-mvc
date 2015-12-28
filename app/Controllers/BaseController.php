<?php

// app/Controllers/BaseController.php

namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\Debug\Exception\FlattenException;

/**
 * Class - BaseController
 * Base operations for controllers
 *
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class BaseController {

    use \Controllers\Helper\FormTrait;

use \Controllers\Helper\SessionTrait;

use \Controllers\Helper\FlashTrait;

use \Controllers\Helper\SecurityTrait;

use \Controllers\Helper\RequestTrait;

use \Controllers\Helper\ResponseTrait;

use \Controllers\Helper\ValidatorTrait;

use \Controllers\Helper\ViewTrait;

use \Controllers\Helper\TranslateTrait;

use \Controllers\Helper\MailTrait;

use \Controllers\Helper\MonologTrait;

use \Controllers\Helper\DebugTrait;

use \Controllers\Helper\PaginationTrait;

    /**
     * Container application
     * 
     * @var Application 
     */
    protected $app;

    /**
     * Query parameters
     *
     * @var array
     */
    public $params;

    /**
     * Options
     *
     * @var array
     */
    public $opts;

    /**
     * Content
     *
     * @var mixed
     */
    public $content;

    /**
     * Route name
     *
     * @var string
     */
    public $route;

    /**
     * Returns the path being requested relative to the executed script.
     *
     * The path info always starts with a /.
     *
     * Suppose this request is instantiated from /mysite on localhost:
     *
     *  * http://localhost/mysite              returns an empty string
     *  * http://localhost/mysite/about        returns '/about'
     *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
     *  * http://localhost/mysite/about?var=1  returns '/about'
     *
     * @return string The raw path (i.e. not urldecoded)
     *
     * @api
     */
    public $url_path;

    /**
     * Returns the root path from which this request is executed.
     *
     * Suppose that an index.php file instantiates this request object:
     *
     *  * http://localhost/index.php         returns an empty string
     *  * http://localhost/index.php/page    returns an empty string
     *  * http://localhost/web/index.php     returns '/web'
     *  * http://localhost/we%20b/index.php  returns '/we%20b'
     *
     * @return string The raw path (i.e. not urldecoded)
     *
     * @api
     */
    public $url_basepath;

    //----------------------

    public function __construct(Application $app) {

        $this->app = $app;
        $this->iniRoutes();
    }

    //============= INIT =======//

    /**
     * Controll init
     * 
     * @param string $action
     * @return void
     */
    protected function init($action = "index/index") {

        //------ WATCH-Routing --------//
        $this->app['watch']->lap('eApp');

        // Get all params
        $params = $this->_getParams($action);

        // Set this params
        $this->params = $params["params"];

        // Set this options
        $this->opts = $params["opts"];

        // Set this content
        $this->content = $params["content"];

        // Set url path 
        $this->url_path = $params["url_path"];

        // Set url basepath 
        $this->url_basepath = $params["url_basepath"];

        // Set route 
        $this->route = $this->app["route"];

        // Set params/opts for services
        $this->app["my.opts"] = $params["opts"];
        $this->app["my.params"] = $params["params"];

        // Init session
        $this->iniSession();
    }

    /**
     * Init session
     * 
     * @return void
     */
    protected function iniSession() {
        $request = $this->getRequest();
        $session = $this->getSession();
        //-----------------------
        // Set user session
        if ($this->isAuthenticated()) {
            $user = $this->getUser();
            $username = $user->getUsername();
            if (null === $this->getUserSession()) {
                $userSession = $this->setUserSession(array('username' => $username));
            }
        }
        // Save current url
        $isXmlHttpRequest = $request->isXmlHttpRequest();
        if (
                $this->route !== 'locale' && 
                $this->route !== 'scheme' && 
                !$isXmlHttpRequest) {
            $url = $request->getUri();
            $session->set('prev_url', $url);
        }
    }

    //============= INIT ROUTERS =======//

    /**
     * Routes initialization
     * 
     * @return void
     */
    protected function iniRoutes() {
        
    }

    //============= GET PARAMS =======//

    /**
     * Get all params
     * 
     * @param string $action
     * @return array
     */
    protected function _getParams($action = "index/index") {
        $params = array();
        $opts = array();
        $request = $this->getRequest();
        //-----------------
        // Get all query params
        $params += $request->query->all();

        // Get all post params
        $params += $request->request->all();

        // Get PathInfo
        $pathInfo = $request->getPathInfo();
        $pathInfo = trim($pathInfo, "/");

        // Get PathInfo
        $basePath = $request->getBasePath();
        $basePath = trim($pathInfo, "/");

        // Get content
        $content = $request->getContent();
        // Set environment value (production, test)
        $opts["environment"] = $this->app['config']['parameters']['environment'];
        // Set debug value
        $opts["debug"] = $this->app['config']['parameters']['debug'];

        $opts += $this->getContrlAction($action);

        return array("opts" => $opts, "params" => $params, 'content' => $content, 'url_path' => $pathInfo, 'url_basepath' => $basePath);
    }

    //============= LOG =======//

    /**
     * Save the data in the log
     * 
     * @param  array $msgs 
     * @return void
     */
    protected function saveLog($msgs = array()) {
        $sysBox = $this->app['my']->get('system');
        $config = $this->app["my"]->get('config');
        //------------------
        // Запишем лог
        $msg = $this->getLogMsg($msgs);
        $file = $config->getProjectPath("logs") . "/user.log";
        $sysBox->saveLog($msg, $file);
    }

    /**
     * Save error log
     * 
     * @param  array $msgs 
     * @return void
     */
    protected function saveErrorLog($msgs = array()) {
        $sysBox = $this->app['my']->get('system');
        $config = $this->app["my"]->get('config');
        //------------------------
        // Запишем лог
        $msg = $this->getLogMsg($msgs);
        $file = $config->getProjectPath("logs") . "/error.log";
        $sysBox->saveLog($msg, $file);
    }

    /**
     * Get log message
     * 
     * @param array $arData 
     * @return string
     *  
     */
    protected function getLogMsg($arData = array()) {
        $results = array();
        $twig = $this->app['twig'];
        $sysBox = $this->app['my']->get('system');
        //----------------
        $date = date("Y-m-d H:i:s");

        if (!array_key_exists("opts", $arData)) {
            $arData["opts"] = $this->opts;
        }

        if (!array_key_exists("params", $arData)) {
            $arData["params"] = $this->params;
        }

        $results["results"] = $sysBox->ArrData2View($arData);

        $results["date"] = $date;
        $results["env"] = $this->opts['environment'];
        $results["controller"] = $this->opts['controller'];
        $results["action"] = $this->opts['action'];


        $content = $twig->render('Include\log.txt.twig', $results);
        $content = htmlspecialchars_decode($content, ENT_QUOTES);

        return $content;
    }

    //============= VIEW =======//

    /**
     * Show pattern
     * 
     * @param array $data
     * @param bool $is_layout
     * @return string
     */
    protected function showView($data = array()) {
        $twig = $this->app['twig'];

        //------ WATCH-Controller --------//
        $this->app['watch']->lap('eApp');

        // Get tpl file
        $tplFile = "Controller/{$this->opts["controller"]}/{$this->opts["action"]}.html.twig";

        if ($data === NULL || !count($data)) {
            $data = $this->opts;
        } else {
            $data += $this->opts;
        }
        // Add flash message data
        if ($this->isFlashMessage()) {
            $arrMsg = $this->getFlashMessage();
            $data += $arrMsg;
        }
        // Convert string message to array message
        if (isset($data['class_message']) && isset($data['messages'])) {
            $messages = $data['messages'];
            if (!is_array($messages)) {
                $data['messages'] = array($messages);
            }
        }
        // Add controller 
        $data['_c_'] = $this;
        $content = $twig->render($tplFile, $data);

        return $content;
    }

    /**
     * Show text markdown markup file
     * file is selected according to the localization
     * 
     * @param string $filename
     * @param string $type Type of Markdown: traditional, github, extra
     * @return string
     */
    public function showMarkdown($filename, $type = 'github') {
        $twig = $this->app['twig'];
        $arBox = $this->app['my']->get('array');
        $strBox = $this->app['my']->get('string');
        $basepath = $this->app['basepath'];
        $locale = $this->app['locale'];
        $title = "";
        $filename = trim($filename);
        $filename = str_replace('\\', '/', $filename);
        //-------------------------------------------
        if (is_file($filename)) {
            $lastFilename = $arBox->set($filename, "/")->getLast();
            // Set title
            $title = $lastFilename;
            // Check word in uppercase
            $upperFilename = $strBox->set($lastFilename)->toUpper()->get();
            $isUpper = ($arBox->set($lastFilename, ".")->get(0) == $arBox->set($upperFilename, ".")->get(0));
            if ($isUpper) {
                $locale = strtoupper($locale);
            }
            // Get the name of the file to a different locale 
            $lastFilename = $arBox->set($lastFilename, ".")->get(0) . "-{$locale}.md";
            $localeFilename = $arBox->set($filename, "/")->pop()->join('/') . "/{$lastFilename}";
            // Get file content
            if (is_file($localeFilename)) {
                // Set title
                $title = $lastFilename;
                $strFile = file_get_contents($localeFilename);
            } else {
                $strFile = file_get_contents($filename);
            }
        } else {

            // Get file name
            $filename = $basepath . '/app/Views/' . $this->getIncPath() . '/' . $filename;
            if (!is_file($filename)) {
                $this->app->abort(404, "File '{$filename}' does not exist.");
            }
            $lastFilename = $arBox->set($filename, "/")->getLast();
            // Set title
            $title = $lastFilename;

            // Check word in uppercase
            $upperFilename = $strBox->set($lastFilename)->toUpper()->get();
            $isUpper = ($arBox->set($lastFilename, ".")->get(0) == $arBox->set($upperFilename, ".")->get(0));
            if ($isUpper) {
                $locale = strtoupper($locale);
            }
            // Get the name of the file to a different locale 
            $lastFilename = $arBox->set($lastFilename, ".")->get(0) . "-{$locale}.md";
            $localeFilename = $arBox->set($filename, "/")->pop()->join('/') . "/{$lastFilename}";
            // Get file content
            if (is_file($localeFilename)) {
                // Set title
                $title = $lastFilename;
                $strFile = file_get_contents($localeFilename);
            } else {
                $strFile = file_get_contents($filename);
            }
        }
        switch ($type) {
            case 'traditional':
                $markdown = $this->app['my']->get('markdown');
                break;
            case 'github':
                $markdown = $this->app['my']->get('markdown_github');
                break;
            case 'extra':
                $markdown = $this->app['my']->get('markdown_extra');
                break;
            default:
                break;
        }
        // Get markdown parser text
        $text = $markdown->parse($strFile);
        // Get twig render content
        $tplFile = "Include/markdown_container.html.twig";
        $content = $twig->render($tplFile, array('title' => $title, 'text' => $text));
        return $content;
    }

    /**
     * Get path to lib
     * 
     * @return string
     */
    protected function getLibPath() {
        $basepath = $this->app['basepath'];
        return $basepath . "/app/Views/Controller/{$this->opts["controller"]}/lib/{$this->opts["action"]}";
    }

    /**
     * Get path to inc
     * 
     * @return string
     */
    protected function getIncPath() {

        return "Controller/{$this->opts["controller"]}/inc/{$this->opts["action"]}";
    }

    /**
     * Get controller and action
     * 
     * @param string $action  (blog/new)
     * @return array
     */
    protected function getContrlAction($action) {
        $strBox = $this->app['my']->get('string');
        $arBox = $this->app['my']->get('array');
        //-----------------------

        $controller = $arBox->set($action, "/")->get(0);
        $action = $arBox->set($action, "/")->get(1);
        $arr["controller"] = $controller;
        $arr["action"] = $action;

        $controller = $arBox->set($controller, "\\")->getLast();
        $controller = $strBox->set($controller)->toLower()->replace("controller", "")->get();
        $action = $strBox->set($action)->toLower()->replace("action", "")->get();

        return array('controller' => $controller, 'action' => $action);
    }

    //============= HELPERS =======//

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool true if the service id is defined, false otherwise
     */
    public function has($id) {

        return isset($this->app[$id]);
    }

    /**
     * Gets a container service by its id.
     *
     * @param string $id The service id
     *
     * @return object|NULL The service
     */
    public function get($id) {
        if ($this->has($id)) {
            return $this->app[$id];
        } else {
            return NULL;
        }
    }

    //============= MESSAGES =======//

    /**
     * Get flash message
     *
     * @param string $aBoxMsgType This is box type ('msg_box', 'alert_box','alert_block_box')
     * @return array
     */
    public function getFlashMessage($aBoxMsgType = 'msg_box') {
        if ($this->isFlashMessage()) {
            if ($this->hasFlash('warning_message')) {
                $class_message = ($aBoxMsgType == 'msg_box') ? "alert_warning" : "alert-warning";
                $message = $this->getFlash('warning_message');
            } else if ($this->hasFlash('error_message')) {
                $class_message = ($aBoxMsgType == 'msg_box') ? "alert_danger" : "alert-danger";
                $message = $this->getFlash('error_message');
            } else if ($this->hasFlash('info_message')) {
                $class_message = ($aBoxMsgType == 'msg_box') ? "alert_info" : "alert-info";
                $message = $this->getFlash('info_message');
            } else if ($this->hasFlash('success_message')) {
                $class_message = ($aBoxMsgType == 'msg_box') ? "alert_success" : "alert-success";
                $message = $this->getFlash('success_message');
            }
            $title_message = $this->trans($class_message);
            $arrMsg = array(
                'class_message' => $class_message,
                'title_message' => $title_message,
                'messages' => $message,
                $aBoxMsgType => TRUE
            );
            return $arrMsg;
        }
        return array();
    }

    /**
     * Is flash message
     * 
     * @return bool
     */
    public function isFlashMessage() {
        $result = $this->hasFlash('warning_message') ||
                $this->hasFlash('error_message') ||
                $this->hasFlash('info_message') ||
                $this->hasFlash('success_message');
        return $result;
    }

    //============= ERRORS =======//

    /**
     * Show error
     * 
     * @param \Exception $exc
     * @return string 
     */
    protected function showError(\Exception $exc) {
        $results = array();
        $monolog = $this->app["monolog"];
        $request = $this->app['request'];
        //--------------------
        // Get error code
        $code = $this->getCodeException($exc);

        // Prepare data for output
        $results["code"] = $code;
        $results["message"] = $exc->getMessage();

        // Save error log
        $this->saveErrorLog($results);

        $format = $request->getRequestFormat();
        if ($this->app['debug'] && $format == 'html') {
            throw $exc;
        } else {
            // Add message to "Monolog"
            $monolog->addError("{$code}. {$results["message"]}", $this->opts + $this->params);
            $exception = FlattenException::create($exc, $code);
            $controller = new ErrorController($this->app);
            $response = $controller->showAction($request, $exception);
        }
        return $response;
    }

    /**
     * The error message for validation:
     *  - error message;
     *  
     * @return string
     *
     */
    protected function getMsgErrorValid($errors) {
        $message = "";
        $sysBox = $this->app['my']->get('system');
        $is_debug = $this->app['debug'];
        //------------------------
        $message .= '<em>Message:</em><br><ul>';
        foreach ($errors as $error) {
            $errMsg = $error->getMessage();
            $errProp = $error->getPropertyPath();
            $errValue = $sysBox->varExport($error->getInvalidValue());
            $message .= "<li>errProperty = '{$errProp}'; errValue = '{$errValue}'; errMessage = {$errMsg}</li>";
        }
        $message .= '</ul>';
        return $message;
    }

    /**
     * The error message in the form:
     *  - params;
     *  - error message;
     *  - trace of the error
     * @return string
     *
     */
    private function _getMsgErrorExc(\Exception $exc) {
        $message = "";
        $httpCodes = $this->app["my"]->get('http')->getHttpCodes();
        $sysBox = $this->app['my']->get('system');
        $arBox = $this->app['my']->get('array');
        $is_debug = $this->app['debug'];
        //------------------------
        if ($is_debug) {
            // Get error code
            if ($exc instanceof Exception\HttpException) {
                $code = $exc->getStatusCode();
            } else {
                $code = 400;
            }

            $code = (int) $code;
            if (isset($httpCodes[$code])) {
                $code .= " ({$httpCodes[$code]})";
            }
            $message .= "<em>Code:</em> {$code}<br>";

            $message .= '<em>Params:</em><br><ul>';
            $message .= "<li>{$sysBox->varExport($this->params)}</li>";
            $message .= '</ul>';
        }
        $message .= $exc->getMessage() . '<br>';
        if ($is_debug) {
            $message .= '<em>Trace Error:</em><br><ul>';
            $trace = $exc->getTraceAsString();
            $arrTrace = $arBox->set($exc->getTraceAsString(), '#')->delRows()->shift()->get();
            foreach ($arrTrace as $value) {
                if ($value) {
                    $message .= "<li>{$value}</li>";
                }
            }
            $message .= '</ul>';
        }

        return $message;
    }

    /**
     * Send ajax error
     * 
     * @param \Exception $exc
     * @param int $status The response status code
     * 
     * @return string 
     */
    protected function errorAjax(\Exception $exc) {
        // Get error code
        if ($exc instanceof Exception\HttpException) {
            $code = $exc->getStatusCode();
        } else {
            $code = 400;
        }

        $code = (int) $code;
        $message = $this->_getMsgErrorExc($exc);
        $messages = $this->getAlertMessage($message);
        return $this->sendJson($messages, $code);
    }

    /**
     * Send ajax valid error
     * 
     * @param \Exception $exc
     * @return string 
     */
    protected function errorAjaxValid(\Exception $exc) {
        $result = $exc->getMessage();
        return $this->sendJson($result);
    }

    /**
     * Get error code
     * 
     * @param \Exception $exc
     * @return int
     */
    protected function getCodeException(\Exception $exc) {
        $httpCodes = $this->app["my"]->get('http')->getHttpCodes();
        //----------------------------
        // Get error code
        if ($exc instanceof Exception\HttpException) {
            $code = $exc->getStatusCode();
        } else {
            $code = $exc->getCode();
        }

        $code = (int) $code;
        if (isset($httpCodes[$code])) {
            $code .= " {$httpCodes[$code]}";
        }

        return $code;
    }

    /**
     * Get alert message
     *
     * @param array|string $aMessage This is message or array messages
     * @param string $aMsgType This is message type ('warning', 'error','info','success')
     * @param string $aBoxType This is box type ('msg_box', 'alert_box','alert_block_box')
     * @return array
     */
    public function getAlertMessage($aMessage, $aMsgType = 'error', $aBoxType = 'msg_box') {

        if (!is_array($aMessage)) {
            $aMessage = array($aMessage);
        }

        if ($aMsgType == 'warning') {
            $class_message = ($aBoxType == 'msg_box') ? "alert_warning" : "alert-warning";
        } else if ($aMsgType == 'error') {
            $class_message = ($aBoxType == 'msg_box') ? "alert_danger" : "alert-danger";
        } else if ($aMsgType == 'info') {
            $class_message = ($aBoxType == 'msg_box') ? "alert_info" : "alert-info";
        } else if ($aMsgType == 'success') {
            $class_message = ($aBoxType == 'msg_box') ? "alert_success" : "alert-success";
        }
        $title_message = $this->trans($class_message);
        $arrMsg = array(
            'class_message' => $class_message,
            'title_message' => $title_message,
            'messages' => $aMessage,
            $aBoxType => TRUE
        );
        return $arrMsg;
    }

}
