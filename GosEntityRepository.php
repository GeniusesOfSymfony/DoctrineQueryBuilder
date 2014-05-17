<?php
namespace Gos\Component\DoctrineQueryBuilder;

use Doctrine\ORM\EntityRepository AS DoctrineEntityRepository;

class GosEntityRepository extends DoctrineEntityRepository implements QueryBuildableInterface
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
    public function createQueryBuilder($group = 'default', QueryBuilderInterface $qb = null)
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

        $namespaceFragment = array(
            $buffer[0],
            $buffer[1],
            'QueryBuilder',
            end($buffer).'QueryBuilder'
        );

        return implode('\\', $namespaceFragment);
    }
}
