<?php

// app/Controllers/TestController.php

namespace Controllers;

use Silex\Application;

/**
 * Class - TestController
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class TestController extends BaseController {

    //-----------------------------
    /**
     * Constructor
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
        $this->app->get('/test', function () use ($self) {
            return $self->indexAction();
        })->bind('test_index');
        $this->app->get('/test/getfile/{path}', function ($path) use ($self) {
            return $self->getfileAction($path);
        })->bind('test_getfile');
        $this->app->get('/test/bootstrap', function () use ($self) {
            return $self->bootstrapAction();
        })->bind('test_bootstrap');
        $this->app->get('/test/todo', function () use ($self) {
            return $self->todoAction();
        })->bind('test_todo');
        $this->app->get('/test/dump', function () use ($self) {
            return $self->dumpAction();
        })->bind('test_dump');
    }

    /**
     * Action - test/index
     * 
     * @return string
     */
    public function indexAction() {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            return $this->showView();
        } catch (Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - test/getfile
     * 
     * @param string $fileName 
     * @return BinaryFileResponse	
     */
    public function getfileAction($fileName) {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            $file = $this->getLibPath() . "/" . $fileName;
            return $this->sendFile($file);
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - test/bootstrap
     * Show bootstrap properties
     * 
     * @return string
     */
    public function bootstrapAction() {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            return $this->showView();
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - test/todo
     * Show todo application
     * 
     * @return string
     */
    public function todoAction() {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            return $this->forwardToRoute("todo");
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - test/dump
     * Show debug info for arbitrary PHP variable
     * 
     * @return string
     */
    public function dumpAction() {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            //  variable defined in PHP, then its dump representation
            $var = array(
                'a simple string' => "in an array of 5 elements",
                'a float' => 1.0,
                'an integer' => 1,
                'a boolean' => true,
                'an empty array' => array(),
            );

            // The gray arrow is a toggle button for hiding/showing children of nested structures.
            $var1 = "This is a multi-line string.\n";
            $var1 .= "Hovering a string shows its length.\n";
            $var1 .= "The length of UTF-8 strings is counted in terms of UTF-8 characters.\n";
            $var1 .= "Non-UTF-8 strings length are counted in octet size.\n";
            $var1 .= "Because of this `\xE9` octet (\\xE9),\n";
            $var1 .= "this string is not UTF-8 valid, thus the `b` prefix.\n";

            $err = new \ErrorException(
                    "For some objects, properties have special values\n"
                    . "that are best represented as constants, like\n"
                    . "`severity` below. Hovering displays the value (`2`).\n", 0, E_WARNING
            );

            return $this->showView(
                            array(
                                'var' => $var,
                                'var1' => $var1,
                                'var3' => $this,
                                'err' => $err
            ));
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

}
