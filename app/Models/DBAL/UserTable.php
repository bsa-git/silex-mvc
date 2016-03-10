<?php

// app/Models/DBAL/UserTable.php

namespace Models\DBAL;

use Doctrine\DBAL\Schema\Table;

/**
 * Class UserTable
 *
 * @category Model
 * @package  app\Models\DBAL
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
final class UserTable extends Table {

    /**
     * Overrides parent constructor
     * @param string $tableName The table name
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct($tableName = 'user') {
        parent::__construct($tableName);

        $this->_build();
    }

    /**
     * Build table
     * @return void
     */
    private function _build() {
        $this->addColumn(
                'id', 'integer', array('unsigned' => true, 'autoincrement' => 'auto',)
        );
        $this->addColumn(
                'username', 'string', array('length' => 32, 'notnull' => true)
        );
        $this->addColumn(
                'first_name', 'string', array('length' => 32, 'notnull' => true)
        );
        $this->addColumn(
                'second_name', 'string', array('length' => 32, 'notnull' => true)
        );
        $this->addColumn(
                'email', 'string', array('length' => 127, 'notnull' => true)
        );
        $this->addColumn(
                'personal_mobile', 'string', array('length' => 32, 'notnull' => true)
        );
        $this->addColumn(
                'password', 'string', array('length' => 255, 'notnull' => true)
        );
        $this->addColumn(
                'roles', 'string', array('length' => 255, 'notnull' => true)
        );

        $this->setPrimaryKey(array('id'));
        $this->addUniqueIndex(array('username',));
        $this->addUniqueIndex(array('email',));
    }

}
