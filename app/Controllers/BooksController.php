<?php

// app/Controllers/BooksController.php

namespace Controllers;

use Silex\Application;
use Pagerfanta\Pagerfanta;

/**
 * Class - BooksController
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class BooksController extends BaseController {

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
        $this->app->get('/books', function () use ($self) {
            return $self->indexAction();
        })->bind('books');

        $this->app->get('/authors', function () use ($self) {
            return $self->authorsAction();
        })->bind('authors');

        $this->app->get('/book/{id}.html', function ($id) use ($self) {
            return $self->bookAction($id);
        })->bind('book');

        $this->app->match('/edit', function () use ($self) {
                    return $self->editAction();
                })
                ->bind('edit')
                ->method('GET|POST');
    }

    /**
     * Action - books/index
     * 
     * @return string
     */
    public function indexAction() {
        $this->init(__CLASS__ . "/" . __FUNCTION__);
        return $this->showView();
    }

    /**
     * Action - books/authors
     * 
     * @return string
     */
    public function authorsAction() {
        $app = $this->app;
        $ipp = 1;
        $authors = array();
        //---------------------
        try {

            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

//            $this->app->abort(406, "Invalid format XML");

            $p = $app['request']->get('p', 1);
            $adapter = new \Pagerfanta\PfAdapter('Models\AuthorModel', array('conditions' => 'id < 1000', 'order' => 'id DESC'));
            $pagerfanta = new Pagerfanta($adapter);
            $pagerfanta->setMaxPerPage($ipp);
            $pagerfanta->setCurrentPage($p);
            $view = new \Pagerfanta\View\DefaultView();
            $html = $view->render($pagerfanta, function($p) use ($app) {
                return $app['url_generator']->generate('authors', array('p' => $p));
            }, array(
                'proximity' => 3,
                'previous_message' => '« Предыдущая', 'next_message' => 'Следующая »'
            ));


            $results = $pagerfanta->getCurrentPageResults();
            foreach ($results as $author) {
                $authors[] = array('name' => $author->name, 'books' => $author->books);
            }
            $data = array('authors' => $authors, 'html' => $html, 'pagerfanta' => $pagerfanta);

            return $this->showView($data);
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - books/book
     * 
     * @param int $id Unique code the book
     * @return string
     */
    public function bookAction($id) {
        $book = \Models\BookModel::find_by_id($id);
        $app = $this->app;
        //-----------------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            if (!$book) {
                $app->abort(404, "Book {$id} does not exist.");
            }
            return $this->showView(array('book' => array('name' => $book->name, 'author' => $book->author)));
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

    /**
     * Action - books/edit
     * 
     * @return string
     */
    public function editAction() {
        $app = $this->app;
        //-----------------------
        try {

            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            $form = new \HTML_QuickForm2('author', 'post', array('action' => ""));
            $form->addElement('text', 'name')->setlabel('Имя автора')->addRule('required', 'Поле обязательно для заполнения');
            $form->addElement('button', null, array('type' => 'submit'))->setContent('ОК');
            if ($form->isSubmitted() && $form->validate()) {
                $values = $form->getValue();
                $author = new \Models\AuthorModel();
                $author->name = $values['name'];
                $author->save();
                // post POST redirect       
                return new \Symfony\Component\HttpFoundation\RedirectResponse($app['url_generator']->generate('authors'));
            }

            return $this->showView(array('form' => $form));
        } catch (\Exception $exc) {
            return $this->showError($exc);
        }
    }

}
