<?php

// app/Models/Helper/DbalTrait.php

namespace Models\Helper;


/**
 * Trait - DbalTrait
 * Doctrine DBAL operations
 *
 * @category Helper
 * @package  app\Models\Helper
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
trait DbalTrait
{
    /**
     * Get the Query Builder for DBAL.
     *
     *
     * @return \Doctrine\DBAL\QueryBuilder
     */
    protected function getDbQueryBuilder() {
        if(! isset($this->app['db'])){
            throw new \LogicException('The \"DoctrineServiceProvider\" is not registered in your application.');
        }
        
        return $this->app['db']->createQueryBuilder();
    }
}
