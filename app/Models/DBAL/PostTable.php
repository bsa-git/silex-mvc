<?php

// app/Models/DBAL/PostTable.php

namespace Models\DBAL;

use Doctrine\DBAL\Schema\Table;

/**
 * Class PostTable
 *
 * @category Model
 * @package  app\Models\DBAL
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class PostTable extends Table {

    /**
     * Overrides parent constructor
     * @param string $tableName The table name
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct($tableName = 'post') {
        parent::__construct($tableName);

        $this->_build();
    }

    /**
     * Build table
     * @return void
     */
    private function _build() {// created
        $this->addColumn(
                'id', 'integer', array('unsigned' => true, 'autoincrement' => 'auto',)
        );
        $this->addColumn(
                'created', 'date', array('notnull' => true)
        );
        $this->addColumn(
                'title', 'string', array('length' => 255, 'notnull' => true)
        );
        $this->addColumn(
                'body', 'text', array('notnull' => true)
        );
        $this->setPrimaryKey(array('id'));
        $this->addUniqueIndex(array('title',));
    }

}
