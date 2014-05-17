<?php
namespace Gos\Component\DoctrineQueryBuilder\Tests\Builder;

use Doctrine\ORM\EntityManager;
use Gos\Component\DoctrineQueryBuilder\QueryBuilder;
use Gos\Component\DoctrineQueryBuilder\Tests\Fixture\BarQueryBuilder;
use Gos\Component\DoctrineQueryBuilder\Tests\Fixture\FooQueryBuilder;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    protected function getEntityManager()
    {
        return $this->getMock('\Doctrine\ORM\EntityManager',
            array(), array(), '', false)
        ;
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
            array('configure'),
            array($this->getEntityManager())
        );

        $qb->expects($this->exactly(1))->method('configure');
        $qb->expects($this->at(0))->method('configure')->with('default');

        $qb = $qb->load();

        $this->assertInstanceOf('Gos\Component\DoctrineQueryBuilder\QueryBuilderInterface', $qb);
    }

    public function testLoadWithString()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            array('configure'),
            array($this->getEntityManager())
        );

        $qb->expects($this->exactly(1))->method('configure');
        $qb->expects($this->at(0))->method('configure')->with('group1');

        $qb = $qb->load('group1');

        $this->assertInstanceOf('Gos\Component\DoctrineQueryBuilder\QueryBuilderInterface', $qb);
    }

    public function testLoadWithArray()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            array('configure'),
            array($this->getEntityManager())
        );

        $qb->expects($this->exactly(3))->method('configure');

        $qb->expects($this->at(0))->method('configure')->with($this->equalTo('group1'));
        $qb->expects($this->at(1))->method('configure')->with($this->equalTo('group2'));
        $qb->expects($this->at(2))->method('configure')->with($this->equalTo('group3'));

        $qb = $qb->load(array('group1', 'group2', 'group3'));

        $this->assertInstanceOf('Gos\Component\DoctrineQueryBuilder\QueryBuilderInterface', $qb);
    }

    /**
     * @expectedException \Exception
     */
    public function testWrongReturnFilters()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            array('registerFilters'),
            array($this->getEntityManager())
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
            array('registerFilters'),
            array($this->getEntityManager())
        );

        $qb->expects($this->any())->method('registerFilters')->will($this->returnValue(array('active' => 'onlyActive')));

        $qb->applyFilter('wrongFilterName', array('foo' => 'bar'));
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingMethodFilter()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            array('registerFilters'),
            array($this->getEntityManager())
        );

        $qb->expects($this->any())->method('registerFilters')->will($this->returnValue(array('active' => 'onlyActive')));

        $qb->applyFilter('active');
    }

    public function testFilter()
    {
        $qb = $this->getMock('Gos\Component\DoctrineQueryBuilder\QueryBuilder',
            array('registerFilters', 'onlyActive'),
            array($this->getEntityManager())
        );

        $qb->expects($this->any())->method('registerFilters')->will($this->returnValue(array('active' => 'onlyActive')));
        $qb->expects($this->any())->method('onlyActive');

        $qb->applyFilter('active');
    }

    public function testSetDefaultTable()
    {
        $class = new \ReflectionClass('Gos\Component\DoctrineQueryBuilder\QueryBuilder');
        $method = $class->getMethod('setDefaultTable');
        $method->setAccessible(true);

        $parameters = array();
        $method->invokeArgs(new QueryBuilder($this->getEntityManager()), array('foo', &$parameters));

        $this->assertEquals(array('table' => 'foo'), $parameters);
    }

    public function testSetNoDefaultTable()
    {
        $class = new \ReflectionClass('Gos\Component\DoctrineQueryBuilder\QueryBuilder');
        $method = $class->getMethod('setDefaultTable');
        $method->setAccessible(true);

        $parameters = array('table' => 'foo');
        $method->invokeArgs(new QueryBuilder($this->getEntityManager()), array('bar', &$parameters));

        $this->assertEquals(array('table' => 'foo'), $parameters);
    }


} 