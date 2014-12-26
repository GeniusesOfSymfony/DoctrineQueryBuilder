<?php
namespace Gos\Component\DoctrineQueryBuilder;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

class GosEntityRepository extends DoctrineEntityRepository implements QueryBuildableInterface
{
    /**
     * Repository as service, avoid to resolve queryBuilderClass each time
     * @var string
     */
    protected $queryBuilderClass;

    /**
     * @param string|null           $group
     * @param QueryBuilderInterface $qb
     *
     * @return QueryBuilderInterface
     */
    public function loadQueryBuilder($group = 'default', QueryBuilderInterface $qb = null)
    {
        if (null === $qb) {
            if (null === $this->queryBuilderClass) {
                $this->queryBuilderClass = $this->getQueryBuilderClass($this->getEntityName());
            }

            $qb = new $this->queryBuilderClass($this->getEntityManager());
        }

        $qb->setEntityName($this->getEntityName());

        $qb->load($group);

        return $qb;
    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    public function getQueryBuilderClass($entityName)
    {
        $buffer = explode('\\', $entityName);

        $count = count($buffer);
        $buffer[$count-2] = 'QueryBuilder';
        $buffer[$count-1] =  end($buffer) . 'QueryBuilder';

        return implode('\\', $buffer);
    }
}
