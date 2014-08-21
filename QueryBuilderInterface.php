<?php
namespace Gos\Component\DoctrineQueryBuilder;

interface QueryBuilderInterface
{
    /**
     * @return QueryBuilder|null
     */
    public function load($groups);

    /**
     * @return string
     */
    public function getEntityName();

    /**
     * @param string $filterName
     *
     * @return QueryBuilder
     */
    public function applyFilter($filterName, $parameters = []);
}
