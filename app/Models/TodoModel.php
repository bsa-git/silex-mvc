<?php

// app/Models/TodoModel.php

namespace Models;

use Models\ORM\Todo;

/**
 * Class - TodoModel
 * Model for todo lists
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class TodoModel extends BaseModel {
    //------------------------

    /**
     * createTask
     * Add new task
     * 
     * @param array $data
     * @return void
     */
    public function createTask($data) {
        $db = $this->app['db'];
        //-----------------------
        if (isset($data['id'])) {
            unset($data['id']);
        }
        $db->insert('todo', $data);
        $id = (int) $db->lastInsertId();
        $data['id'] = $id;
        return $data;
    }

    /**
     * updateTask
     * Update this task
     * 
     * @param array $data
     * @return void
     */
    public function updateTask($data) {
        $db = $this->app['db'];
        //---------------------
        $id = $data['id'];
        unset($data['id']);
        $db->update('todo', $data, array('id' => $id));
        return $data;
    }

    /**
     * deleteTask
     * Delete this task
     * 
     * @param string $id
     * @return void
     */
    public function deleteTask($id) {
        $db = $this->app['db'];
        //--------------------
        $db->delete('todo', array('id' => $id));
    }

    /**
     * readTask
     * Read task
     * 
     * @param string $id
     * @return array
     */
    public function readTask($id) {
        $db = $this->app['db'];
        $norm_tasks = array();
        //--------------------
        // Get user's posts
        $sql = "SELECT * FROM todo WHERE id = ?";
        $task = $db->fetchAll($sql, array($id));
        if (is_array($task) && count($task)) {
            $norm_tasks[] = Todo::normalizeValues($task, $this->app);
        } 
        return $norm_tasks;
    }

    /**
     * readTasks
     * Read tasks
     * 
     * @return array
     */
    public function readTasks() {
        $db = $this->app['db'];
        $norm_tasks = array();
        //--------------------
        // Get user's posts
        $sql = "SELECT * FROM todo";
        $tasks = $db->fetchAll($sql);
        if (is_array($tasks) && count($tasks)) {
            foreach ($tasks as $task) {
                $norm_tasks[] = Todo::normalizeValues($task, $this->app);
            }
        } 
        return $norm_tasks;
    }

}
