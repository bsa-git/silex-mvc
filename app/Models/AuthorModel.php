<?php

// app/Models/AuthorModel.php

namespace Models;

/**
 * Description of Author
 *
 * @author ASUTP   'Model\Book'
 */
class AuthorModel extends \ActiveRecord\Model {

    static $table_name = 'author';
    static $has_many = array(
        array(
            'books',
            'foreign_key' => 'author_id',
            'class_name' => 'BookModel'
        ),
    );

}
