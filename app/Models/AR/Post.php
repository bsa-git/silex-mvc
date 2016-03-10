<?php

// app/Models/AR/Post.php

namespace Models\AR;

/**
 * Class - Post 
 * Model for Post
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class Post extends Model {

    use \Controllers\Helper\TranslateTrait;

    /**
     * Table name
     * @var string 
     */
    static $table_name = 'post';


    //=== ASSOCIATIONS ===//

    /**
     * Belongs to
     * @var array
     */
    static $belongs_to = array(
        array(
            'user',
            'class_name' => 'User',
            'foreign_key' => 'user_id'
        ),
    );

    //=== GETTERS ===//

    public function get_created() {
        if ($this->app) {
            $locale = $this->getLocale();
        } else {
            $locale = 'en';
        }
        $format = $locale == 'ru' ? 'd.m.Y' : 'm/d/Y';
        $created = $this->read_attribute('created');
        if ($created) {
            $created = $created->format($format);
        }
        return $created;
    }

    /**
     * Set created
     * 
     * @param string $date
     * @return void
     */
    public function set_created($date) {
        $dt = new \DateTime($date);
        $this->assign_attribute('created', $dt->format('Y-m-d'));
    }

    //=== VALIDATION ===//

    /**
     * validates
     * @var array
     */
    static $validates_presence_of = array(
        array('created', 'title', 'body')
    );

    /**
     * Validates can't be blank
     * @var array
     */
    static $validates_size_of = array(
        array('title', 'minimum' => 5)
    );

    //=== PAGINATION ===//

    /**
     * Get paginator
     * 
     * @param int $page
     * @param function $routeGenerator
     * @param array $params
     * @return string
     */
    public function getPaginator($page, $routeGenerator, $params = array()) {
        $db = $this->app['ar'];
        $arrResult = array();
        //----------------------
        // Create Paginator
        $adapter = $this->createPaginatorAdapter('\Pagerfanta\ArAdapter', $db->getClass('post'), $params);
        $pagerfanta = $this->createPaginator($adapter);
        $pagerfanta->setCurrentPage($page);
        $arrResult['html'] = $this->renderPaginator($pagerfanta, $routeGenerator);
        $arrResult['data'] = $pagerfanta->getCurrentPageResults();
        return $arrResult;
    }

}
