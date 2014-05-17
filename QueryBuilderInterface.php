<?php
namespace Gos\Component\DoctrineQueryBuilder;

interface QueryBuilderInterface
{
    public function load($groups);

    public function getEntityName();

    public function applyFilter($filterName, $parameters = array());
}
