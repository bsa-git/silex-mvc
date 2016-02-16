<?php

// app/Models/AR/User.php

namespace Models\AR;

/**
 * Class - User 
 * Model for User
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
*/
class User extends Model {

    /**
     * Table name
     * @var string 
     */
    static $table_name = 'user';
    
    /**
     * Has many
     * @var array
     */
    static $has_many = array(
        array(
            'post',
            'foreign_key' => 'user_id',
            'class_name' => 'Post'
        ),
    );
}
