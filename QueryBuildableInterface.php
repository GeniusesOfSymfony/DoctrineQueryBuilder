<?php
namespace Gos\Component\DoctrineQueryBuilder;

interface QueryBuildableInterface
{
    /**
     * @param string                $group
     * @param QueryBuilderInterface $qb
     *
     * @return QueryBuilderInterface
     */
    public function loadQueryBuilder($group = null, QueryBuilderInterface $qb = null);
}
