<?php
namespace Gos\Component\DoctrineQueryBuilder;

interface QueryBuildableInterface
{
    /**
     * @param string $entityName
     *
     * @return mixed
     */
    public function getQueryBuilderClass($entityName);

    /**
     * @param string                $group
     * @param QueryBuilderInterface $qb
     *
     * @return QueryBuilderInterface
     */
    public function createQueryBuilder($group = 'default', QueryBuilderInterface $qb = null);
} 