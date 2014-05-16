<?php
namespace Gos\Component\DoctrineQueryBuilder;

use Doctrine\ORM\EntityRepository;

class GosEntityRepository extends EntityRepository
{
    /**
     * Repository as service, avoid to resolve queryBuilderClass each time
     * @var string
     */
    protected $queryBuilderClass;

    /**
     * @param string $group
     *
     * @return QueryBuilderInterface
     */
    public function createQueryBuilder($group = 'default')
    {
        if(null === $this->queryBuilderClass){
            $buffer = explode('\\', $this->getEntityName());

            $namespaceFragment = array(
                $buffer[0],
                $buffer[1],
                'QueryBuilder',
                end($buffer).'QueryBuilder'
            );

           $this->queryBuilderClass = implode('\\', $namespaceFragment);
        }

        $qb = new $this->queryBuilderClass($this->getEntityManager());
        $qb->setEntityName($this->getEntityName());
        $qb->load($group);

        return $qb;
    }
}