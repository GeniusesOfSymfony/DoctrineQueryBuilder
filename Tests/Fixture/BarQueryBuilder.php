<?php
namespace Gos\Component\DoctrineQueryBuilder\Tests\Fixture;

use Gos\Component\DoctrineQueryBuilder\QueryBuilder;

class BarQueryBuilder extends QueryBuilder
{
    public function configure($group)
    {
        if($group === 'default'){
            $this->select('tbl');
            $this->from($this->getEntityName(), 'tbl');
        }
    }
} 