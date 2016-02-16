<?php

// app/Models/Helper/OrmTrait.php

namespace Models\Helper;


/**
 * Trait - OrmTrait
 * Doctrine ORM operations
 *
 * @category Helper
 * @package  app\Models\Helper
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
trait OrmTrait
{
    /**
     * Gets the repository for an entity class.
     *
     * @param string $entityName The name of the entity.
     *
     * @return \Doctrine\ORM\EntityRepository The repository class.
     */
    protected function getRepository($entityName) {
        if(! isset($this->app['em'])){
            throw new \LogicException('The \"DoctrineOrmServiceProvider\" is not registered in your application.');
        }
        
        return $this->app['em']->getRepository($entityName);
    }
    
    /**
     * Get the Query Builder for ORM.
     *
     *
     * @return \Doctrine\ORM\QueryBuilder    $queryBuilder = $conn->createQueryBuilder();
     */
    protected function getEmQueryBuilder() {
        if(! isset($this->app['em'])){
            throw new \LogicException('The \"DoctrineOrmServiceProvider\" is not registered in your application.');
        }
        
        return $this->app['em']->createQueryBuilder();
    }
}
