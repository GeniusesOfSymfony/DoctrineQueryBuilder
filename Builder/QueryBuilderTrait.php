<?php
namespace Gos\Component\DoctrineQueryBuilder\Builder;

trait QueryBuilderTrait
{
    /**
     * @var QueryBuilderInterface
     */
    protected $queryBuilder;

    /**
     * @param QueryBuilderInterface $queryBuilder
     */
    public function setQueryBuilder(QueryBuilderInterface $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param $group
     *
     * @return QueryBuilderInterface
     */
    public function getQueryBuilder($group)
    {
        return $this->queryBuilder->load($group);
    }
}