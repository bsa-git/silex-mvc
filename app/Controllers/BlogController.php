<?php

// app/Controllers/BlogController.php

namespace Controllers;

use Silex\Application;
use Models\ORM\Post;
use Forms\PostForm;

/**
 * Class - BlogController
 * Run blog functions
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class BlogController extends BaseController {

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
        $this->app->get('/blog', function () use ($self) {
            return $self->indexAction();
        })->bind('blog');
        $this->app->get('/blog/posts/{username}', function ($username) use ($self) {
            return $self->postsAction($username);
        })->bind('blog_posts');
        $this->app->get('/blog/posts', function () use ($self) {
            return $self->postsAction(null);
        })->bind('blog_posts_all');
        $this->app->match('/blog/new', function () use ($self) {
                    return $self->newAction();
                })
                ->bind('blog_new')
                ->method('GET|POST');
        ;
        $this->app->match('/blog/edit/{id}', function ($id) use ($self) {
                    return $self->editAction($id);
                })
                ->bind('blog_edit')
                ->method('GET|POST');
        ;
        $this->app->match('/blog/delete/{id}', function ($id) use ($self) {
                    return $self->deleteAction($id);
                })
                ->bind('blog_delete');
        ;
    }

    /**
     * Action - blog/index
     * 
     * @return string
     */
    public function indexAction() {
        $models = $this->app['models'];
        //--------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            // Get posts
            $data = $models->load('Post', 'getPosts', null);
            return $this->showView($data);
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - blog/posts
     * receive posts written by the user
     * 
     * @param string $userName 
     * @return string
     */
    public function postsAction($userName) {
        $models = $this->app['models'];
        //--------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            // Get posts
            if ($userName) {
                $data = $models->load('Post', 'getPosts', $userName);
            } else {
                $data = $models->load('Post', 'getPosts', null);
            }
            return $this->showView($data);
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - blog/new
     * add new post for current user
     * 
     * @return string
     */
    public function newAction() {
        $request = $this->getRequest();
        $models = $this->app['models'];
        $locale = $this->getLocale();
        $format = $locale == 'ru' ? 'd.m.Y' : 'm/d/Y';
        //--------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            // Create object NewPost and set values
            $newPost = new Post();
            $newPost->setCreated(date($format));
            // Create form
            $form = $this->createForm(new PostForm(), $newPost, array('action' => "/blog/new"));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->getUser();
                $username = $user->getUsername();
                // Set field "created" to datetime object
                $created = $newPost->getCreated();
                $newPost->setCreated(date_create($created));

                $models->load('Post', 'newPost', array('username' => $username, 'new_post' => $newPost));

                $this->addFlash('info_message', $this->trans('added_new_message', array('{{ title }}' => $newPost->getTitle())));

                return $this->redirect("/account");
            }

            // Show form
            return $this->showView(array('form' => $form->createView()));
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - blog/edit
     * edit post for current user
     * 
     * @param int $id 
     * @return string
     */
    public function editAction($id) {
        $models = $this->app['models'];
        $request = $this->getRequest();
        $locale = $this->getLocale();
        $format = $locale == 'ru' ? 'd.m.Y' : 'm/d/Y';
        $em = $this->app['em'];
        $repo = $em->getRepository('Models\ORM\Post');
        //--------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            // Find edit post
            $editPost = $repo->find($id);

            // Set field "created" to string format
            $created = $editPost->getCreated();
            $created = date_format($created, $format);
            $editPost->setCreated($created);

            // Create form
            $form = $this->createForm(new PostForm(), $editPost, array('action' => "/blog/edit/{$id}"));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Set field "created" to datetime object
                $created = $editPost->getCreated();
                $editPost->setCreated(date_create($created));
                // Save post
                $models->load('Post', 'editPost', array('edit_post' => $editPost));

                $this->addFlash('info_message', $this->trans('edited_this_message', array('{{ title }}' => $editPost->getTitle())));

                return $this->redirect("/account");
            }
            // Show form
            return $this->showView(array('form' => $form->createView()));
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - blog/delete
     * delete post for current user
     * 
     * @param int $id 
     * @return string
     */
    public function deleteAction($id) {
        $request = $this->getRequest();
        $models = $this->app['models'];
        $em = $this->app['em'];
        $repo = $em->getRepository('Models\ORM\Post');
        //--------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            // Find edit post
            $deletePost = $repo->find($id);

            $em->remove($deletePost);
            $em->flush();

            $this->addFlash('info_message', $this->trans('deleted_this_message', array('{{ title }}' => $deletePost->getTitle())));

            return $this->redirect("/account");
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

}
