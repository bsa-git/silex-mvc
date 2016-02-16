<?php

// app/Models/AR/Todo.php

namespace Models\AR;

/**
 * Class - Todo 
 * Model for Todo
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class Todo extends Model {

    /**
     * Table name
     * @var string 
     */
    static $table_name = 'todo';

    //=== GETTERS ===//

    /**
     * Get id
     * @return int
     */
    public function get_id() {
        return (int) $this->read_attribute('id');
    }

    /**
     * Get task_order
     * @return int
     */
    public function get_task_order() {
        return (int) $this->read_attribute('task_order');
    }

    /**
     * Get done
     * @return boolean
     */
    public function get_done() {
        return (boolean) $this->read_attribute('done');
    }

    /**
     * Get title
     * @return string
     */
    public function get_title() {
        $value = $this->read_attribute('title');
        if ($this->has('zf2')) {
            $nValue = $this->app['zf2']
                    ->get('filter')
                    ->attach($this->app['zf2.filter.string_trim']())
                    ->attach($this->app['zf2.filter.strip_tags']())
                    ->filter($value);
            return $nValue;
        } else {
            return $value;
        }
    }

    /**
     * Set id
     * 
     * @param int $id
     * @return void
     */
    public function set_id($id) {
        $id = (int) $id;
        $this->assign_attribute('id', $id);
    }

    /**
     * Set task_order
     * 
     * @param int $task_order
     * @return void
     */
    public function set_task_order($task_order) {
        $task_order = (int) $task_order;
        $this->assign_attribute('task_order', $task_order);
    }

    /**
     * Set done
     * 
     * @param boolean $done
     * @return void
     */
    public function set_done($done) {
        $done = (boolean) $done;
        $this->assign_attribute('done', $done);
    }

    /**
     * Set title
     * 
     * @param string $title
     * @return void
     */
    public function set_title($title) {
        if ($this->has('zf2')) {
            $title = $this->app['zf2']
                    ->get('filter')
                    ->attach($this->app['zf2.filter.string_trim']())
                    ->attach($this->app['zf2.filter.strip_tags']())
                    ->filter($title);
        }
        $this->assign_attribute('title', $title);
    }

}
