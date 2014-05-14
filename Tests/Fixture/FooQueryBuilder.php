<?php
namespace Gos\Component\DoctrineQueryBuilder\Tests\Fixture;

use Gos\Component\DoctrineQueryBuilder\QueryBuilder;

class FooQueryBuilder extends QueryBuilder
{
    public function registerFilters()
    {
        return array('foo' => 'bar', 'bug' => 'really');
    }

    public function configure($group)
    {
        if($group === 'default'){
            $this->select('tbl');
            $this->from($this->getEntityName(), 'tbl');
        }

        if($group === 'test'){
            $this->orderBy('tlb.field', 'ASC');
        }
    }

    protected function bar($parameter)
    {
        $this->where('tbl.field = :thing');
        $this->setParameter('thing', $parameter['foo']);
    }
} 