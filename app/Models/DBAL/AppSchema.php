<?php

// app/Models/Schema/AppSchema.php
namespace Models\DBAL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;

/**
 * Class SecuritySchema
 *
 * @category PHPClass
 * @package  Fluency\Silex\Security\Core\Dbal
 * @author   Rafael Ernesto Espinosa Santiesteban <ralphlnx@gmail.com>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://fluency.inc.com
 */
class AppSchema extends Schema {

    /**
     * Overrides parent constructor
     *
     * @param \Doctrine\DBAL\Schema\Table[]      $tables       The schema tables
     * @param \Doctrine\DBAL\Schema\Sequence[]   $sequences    The schema sequences
     * @param \Doctrine\DBAL\Schema\SchemaConfig $schemaConfig The schema config
     * @param array                              $namespaces   Namespaces for tables
     */
    public function __construct(array $tables = array(), array $sequences = array(), SchemaConfig $schemaConfig = null, array $namespaces = array()) {

        parent::__construct($tables, $sequences, $schemaConfig, $namespaces);

        $this->_addTable($userTable = new UserTable());
        $this->_addTable($postTable = new PostTable());
        $this->_addTable($todoTable = new TodoTable());

        $this->getTable($postTable->getName())->addColumn(
                'user_id', 'integer', array('unsigned' => true, 'notnull' => true,)
        );
        $this->getTable($postTable->getName())->addForeignKeyConstraint(
                'user', array('user_id'), array('id'), array('onDelete' => 'CASCADE', 'onUpdate' => 'CASCADE')
        );
    }

}
