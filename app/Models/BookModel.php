<?php

// app/Models/BookModel.php

namespace Models;

/**
 * Description of Book
 *
 * @author ASUTP
 */
class BookModel extends \ActiveRecord\Model {

    static $table_name = 'book';
    static $belongs_to = array(
        array(
            'author', 
            'class_name' => 'AuthorModel', 
            'foreign_key' => 'author_id'
            ),
        );

}
