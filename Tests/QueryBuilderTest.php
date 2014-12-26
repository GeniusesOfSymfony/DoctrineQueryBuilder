<?php
namespace Gos\Component\DoctrineQueryBuilder\Tests;

use Gos\Component\DoctrineQueryBuilder\QueryBuilder;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getMock('\Doctrine\ORM\EntityManager',
            [], [], '', false);
    }

    public function testGetEntityName()
    {
        $qb = new QueryBuilder($this->getEntityManager());
        $qb->setEntityName('entity');
        $this->assertEquals($qb->getEntityName(), 'entity');
    }

    public function testLoadWithoutParameters()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            ['configure'],
            [$this->getEntityManager()]
        );

        $qb->expects($this->exactly(1))->method('configure');
        $qb->expects($this->at(0))->method('configure')->with('default');

        $qb = $qb->load();

        $this->assertInstanceOf('Gos\Component\DoctrineQueryBuilder\QueryBuilderInterface', $qb);
    }

    public function testLoadWithString()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            ['configure'],
            [$this->getEntityManager()]
        );

        $qb->expects($this->exactly(1))->method('configure');
        $qb->expects($this->at(0))->method('configure')->with('group1');

        $qb = $qb->load('group1');

        $this->assertInstanceOf('Gos\Component\DoctrineQueryBuilder\QueryBuilderInterface', $qb);
    }

    public function testLoadWithArray()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            ['configure'],
            [$this->getEntityManager()]
        );

        $qb->expects($this->exactly(3))->method('configure');

        $qb->expects($this->at(0))->method('configure')->with($this->equalTo('group1'));
        $qb->expects($this->at(1))->method('configure')->with($this->equalTo('group2'));
        $qb->expects($this->at(2))->method('configure')->with($this->equalTo('group3'));

        $qb = $qb->load(['group1', 'group2', 'group3']);

        $this->assertInstanceOf('Gos\Component\DoctrineQueryBuilder\QueryBuilderInterface', $qb);
    }

    /**
     * @expectedException \Exception
     */
    public function testWrongReturnFilters()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            ['registerFilters'],
            [$this->getEntityManager()]
        );

        $qb->expects($this->any())->method('registerFilters')->will($this->returnValue(false));

        $qb->applyFilter('foo');
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingFilterRegistration()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            ['registerFilters'],
            [$this->getEntityManager()]
        );

        $qb->expects($this->any())->method('registerFilters')->will($this->returnValue(['active' => 'onlyActive']));

        $qb->applyFilter('wrongFilterName', ['foo' => 'bar']);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingMethodFilter()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            ['registerFilters'],
            [$this->getEntityManager()]
        );

        $qb->expects($this->any())->method('registerFilters')->will($this->returnValue(['active' => 'onlyActive']));

        $qb->applyFilter('active');
    }

    public function testFilter()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            ['registerFilters', 'onlyActive'],
            [$this->getEntityManager()]
        );

        $qb->expects($this->any())->method('registerFilters')->will($this->returnValue(['active' => 'onlyActive']));
        $qb->expects($this->any())->method('onlyActive');

        $qb->applyFilter('active');
    }

    public function testSetDefaultTable()
    {
        $class = new \ReflectionClass('Gos\Component\DoctrineQueryBuilder\QueryBuilder');
        $method = $class->getMethod('setDefaultTable');
        $method->setAccessible(true);

        $parameters = [];
        $method->invokeArgs(new QueryBuilder($this->getEntityManager()), ['foo', &$parameters]);

        $this->assertEquals(['table' => 'foo'], $parameters);
    }

    public function testSetNoDefaultTable()
    {
        $class = new \ReflectionClass('Gos\Component\DoctrineQueryBuilder\QueryBuilder');
        $method = $class->getMethod('setDefaultTable');
        $method->setAccessible(true);

        $parameters = ['table' => 'foo'];
        $method->invokeArgs(new QueryBuilder($this->getEntityManager()), ['bar', &$parameters]);

        $this->assertEquals(['table' => 'foo'], $parameters);
    }
}
