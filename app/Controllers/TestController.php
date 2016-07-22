<?php

// app/Controllers/TestController.php

namespace Controllers;

use Silex\Application;
use Models\ORM\Post;
use Forms\PostForm;

/**
 * Class - TestController
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
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
        $this->app->get('/test/backbone', function () use ($self) {
            return $self->backboneAction();
        })->bind('test_backbone');
        $this->app->get('/test/vue', function () use ($self) {
            return $self->vueAction();
        })->bind('test_vue');
        $this->app->get('/test/dump', function () use ($self) {
            return $self->dumpAction();
        })->bind('test_dump');
        $this->app->get('/test/addpost', function () use ($self) {
                    return $self->addpostAction();
                })
                ->bind('test_addpost')
                ->method('GET|POST');
        $this->app->get('/test/posts/{username}/{page}', function ($username, $page) use ($self) {
            return $self->postsAction($username, $page);
        })->bind('test_posts_user_page');
        $this->app->get('/test/posts/{username}', function ($username) use ($self) {
            return $self->postsAction($username, null);
        })->bind('test_posts_user');
        $this->app->get('/test/posts', function () use ($self) {
            return $self->postsAction(null, null);
        })->bind('test_posts_all');
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
     * Show todo application list
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
     * Action - test/backbone
     * Show todo application for backbone.js
     * 
     * @return string
     */
    public function backboneAction() {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            return $this->forwardToRoute("todo_bb");
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }
    
    /**
     * Action - test/vue
     * Show todo application for vue.js
     * 
     * @return string
     */
    public function vueAction() {
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            return $this->forwardToRoute("todo_vue");
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - test/addpost
     * add post via Active Record
     * 
     * @return string
     */
    public function addpostAction() {
        $request = $this->getRequest();
        $locale = $this->getLocale();
        $format = $locale == 'ru' ? 'd.m.Y' : 'm/d/Y';
        $db = $this->app['ar'];
        //--------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            // Create object NewPost and set values
            $ormPost = new Post();
            if ($this->isMethod('GET')) {
                $ormPost->setCreated(date($format));
            }
            // Create form
            $form = $this->createForm(new PostForm(), $ormPost, array('action' => "/test/addpost"));
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // Get username
                $user = $this->getUser();
                $username = $user->getUsername();
                // Get user
                $user = $db->find('user', 'first', array('conditions' => "username='{$username}'"));
                // Create model post and set values
                $arPost = $db->create('post', $ormPost->getValues());
                $arPost->user_id = $user->id;
                $arPost->saveModel();
                // Send flash message
                $this->addFlash('info_message', $this->trans('added_new_message', array('{{ title }}' => $ormPost->getTitle())));
                // Go to "/account"
                return $this->redirect("/account");
            }

            // Show form
            return $this->showView(array('form' => $form->createView()));
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - test/posts
     * receive posts written by the user
     * 
     * @param string $userName 
     * @param int $page 
     * @return string
     */
    public function postsAction($userName, $page) {
        $app = $this->app;
        $db = $this->app['ar'];
        //--------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            // Get posts
            if ($userName) {

                // Get user
                $user = $db->find('user', 'first', array('conditions' => "username='{$userName}'"));
                // Get paginator
                $params = array('conditions' => "user_id={$user->id}", 'order' => 'id DESC');
                $pag = $db->create('post')->getPaginator($page, function($page) use ($app) {
                    $route_params = $app['route_params'];
                    return $app['url_generator']->generate('test_posts_user_page', array('username' => $route_params['username'], 'page' => $page));
                }, $params);

                // Data for user's posts
                $data = array('username' => $userName, 'posts' => $pag['data'], 'paginator' => $pag['html']);
            } else {
                $posts = array();
                // Get user's posts
                $users = $db->find('user', 'all', array('select' => 'id, username'));
                foreach ($users as $user) {
                    $posts[$user->username] = $user->post;
                }
                // Data for user's posts
                $data = array('posts' => $posts);
            }
            return $this->showView($data);
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
