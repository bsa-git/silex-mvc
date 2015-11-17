<?php

// app/Controllers/TodoController.php

namespace Controllers;

use Silex\Application;
use Models\ORM\Todo;

/**
 * Class - TodoController
 * class for creating user tasks
 * 
 * @category Controller
 * @package  app\Controllers
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class TodoController extends BaseController {

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
        $this->app->get('/todo', function () use ($self) {
            return $self->indexAction();
        })->bind('todo');
        $this->app->post('/tasks', function () use ($self) {
            return $self->createAction();
        })->bind('task_create');
        $this->app->get('/tasks/{id}', function ($id) use ($self) {
            return $self->readAction($id);
        })->bind('task_read ');
        $this->app->get('/tasks', function () use ($self) {
            return $self->readAction();
        })->bind('tasks_all ');
        $this->app->put('/tasks/{id}', function ($id) use ($self) {
            return $self->updateAction($id);
        })->bind('task_update');
        $this->app->delete('/tasks/{id}', function ($id) use ($self) {
            return $self->deleteAction($id);
        })->bind('task_delete');
    }

    /**
     * Action - todo/index
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
     * Action - todo/create
     * create new task
     * 
     * @return void
     */
    public function createAction() {
        $models = $this->app['models'];
        //-----------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);

            // Create object newTask and set values
            $task = new Todo();
            $task->setApp($this->app);
            $task->setResolveValues($this->params);

            // use a validator to check the values
            $errorList = $this->validate($task);
            if ($errorList !== TRUE && count($errorList)) {
                $errMessage = $this->getMsgErrorValid($errorList);
                $this->app->abort(400, $errMessage);
            }

            $data = $models->load('Todo', 'createTask', $task->getValues());
            return $this->sendJson($data);
        } catch (\Exception $exc) {
            return $this->errorAjax($exc);
        }
    }

    /**
     * Action - todo/read
     * read task 
     * 
     * @param int $id The task id
     * @return string
     */
    public function readAction($id = NULL) {
        $models = $this->app['models'];
        //-----------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            if ($id) {
                $data = $models->load('Todo', 'readTask', $id);
                return $this->sendJson($data);
            } else {
                $data = $models->load('Todo', 'readTasks', NULL);
                return $this->sendJson($data);
            }
        } catch (\Exception $exc) {
            return $this->errorAjax($exc);
        }
    }

    /**
     * Action - todo/update
     * update task 
     * 
     * @param int $id The task id
     * @return string
     */
    public function updateAction($id) {
        $models = $this->app['models'];
        //-----------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            // Create object newTask and set values
            $task = new Todo();
            $task->setApp($this->app);
            $task->setResolveValues($this->params);

            // use a validator to check the values
            $errorList = $this->validate($task);
            if ($errorList !== TRUE && count($errorList)) {
                $errMessage = $this->getMsgErrorValid($errorList);
                $this->app->abort(400, $errMessage);
            }

            $data = $models->load('Todo', 'updateTask', $task->getValues());
            return $this->sendJson($data);
        } catch (\Exception $exc) {
            return $this->errorAjax($exc);
        }
    }

    /**
     * Action - todo/delete
     * delete task 
     * 
     * @param int $id The task id
     * @return string
     */
    public function deleteAction($id) {
        $models = $this->app['models'];
        //-----------------
        try {
            // Initialization
            $this->init(__CLASS__ . "/" . __FUNCTION__);
            
            // Delete task
            $models->load('Todo', 'deleteTask', $id);
            
            return $this->sendJson(TRUE);
        } catch (\Exception $exc) {
            return $this->errorAjax($exc);
        }
    }

}
