<?php
namespace Gos\Component\DoctrineQueryBuilder\Tests\Builder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Gos\Component\DoctrineQueryBuilder\GosEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class GosEntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected function getRepository($entityName)
    {
        $em = $this->getMock('Doctrine\ORM\EntityManager', array('getClassMetadata'), array(), '', false);
        $classMetaData = new ClassMetadata($entityName);

        return new GosEntityRepository($em, $classMetaData);
    }

    public function testGetQueryBuilderClass()
    {
        $repository = $this->getRepository('Foo\Bundle\Entity\Bar');
        $this->assertEquals('Foo\Bundle\QueryBuilder\BarQueryBuilder', $repository->getQueryBuilderClass('Foo\Bundle\Entity\Bar'));
    }
}