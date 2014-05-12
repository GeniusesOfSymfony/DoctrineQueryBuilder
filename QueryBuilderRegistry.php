<?php
namespace Gos\Component\DoctrineQueryBuilder;

use Doctrine\ORM\EntityManager;
use Gos\Component\DoctrineQueryBuilder\Builder\QueryBuilderInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class QueryBuilderRegistry
{
    /**
     * @var array
     */
    protected $registry;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->registry = array();
        $this->entityManager = $entityManager;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     */
    public function addQueryBuilder(QueryBuilderInterface $queryBuilder)
    {
        $this->registry[$queryBuilder->getEntityName()] = $queryBuilder;
    }

    /**
     * @param $className
     *
     * @return mixed
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function getQueryBuilder($className)
    {
        if (!isset($this->registry[$className])) {
            throw new ServiceNotFoundException(sprintf('QueryBuilder for %s is actually not load into the WidgetRegistry'), $className);
        }

        return $this->registry[$className];
    }
} 