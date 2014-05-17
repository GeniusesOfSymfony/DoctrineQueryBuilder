Doctrine QueryBuilder Component
==========================================================

[![Build Status](https://travis-ci.org/GeniusesOfSymfony/DoctrineQueryBuilder.svg?branch=master)](https://travis-ci.org/GeniusesOfSymfony/DoctrineQueryBuilder) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/GeniusesOfSymfony/DoctrineQueryBuilder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/GeniusesOfSymfony/DoctrineQueryBuilder/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/GeniusesOfSymfony/DoctrineQueryBuilder/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/GeniusesOfSymfony/DoctrineQueryBuilder/?branch=master) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/35eaee5a-c0e4-427d-aa8c-fd20d8417b88/mini.png)](https://insight.sensiolabs.com/projects/35eaee5a-c0e4-427d-aa8c-fd20d8417b88)

By default Doctrine provide a generic Query Builder, and in each query you need to repopulate it. To avoid to must have rewrite common parts of your builder this component provide a simple way to inject and create a pre populated QueryBuilder according to a specific entity. Avoid DRY, Keep repository healthy, more readable.

Create your own QueryBuilder according to your entity
-----------------------------------------------------

Simply extends your class from `Gos\Component\DoctrineQueryBuilder\QueryBuilder`

```php
use Gos\Component\DoctrineQueryBuilder\QueryBuilder;

class MyQueryBuilder extends QueryBuilder
{
	//stuff
}
```

Or directly create your QueryBuilder on top of DoctrineQueryBuilder

```php
use Doctrine\ORM\QueryBuilder;

class MyQueryBuilder extends QueryBuilder implements QueryBuilderInterface
{
	//stuff
}
```

####Create custom group####

```php
public function configure($group)
{
    if ($group === 'my_group') {
        //Awesome stuff, but never return $qb inside a condition or switch case, that will deny
        //the multi groups populating
    }

    return $qb;
}
```

The group name is not mandatory when you call the createQueryBuilder, in this case `$group = default`

```php
public function configure($group)
{
    if($group === 'my_group'){
        //Awesome stuff
    }

    if($group === 'default'){
        //In the case you not mentioned group name
    }
}
```

####Multiple group####

The QueryBuilder Component have the ability to traverse many groups. With this feature you can split several group in order to reuse inside other without repeat yourself.

```php
public function findSomething()
{
    $qb = $this->createQueryBuilder(array('join_group', 'filtering_group', 'ordering_group'));
    //Your logic
}
```

Register filter on the QueryBuilder
-----------------------------------

We often have filters that return so recurrent in many functions of our repository. In this way you can register filter directly on the QueryBuilder.

To register your own filter :

```php
use Gos\Component\DoctrineQueryBuilder\QueryBuilder;

class PageQueryBuilder extends QueryBuilder
{
    public function registerFilters()
    {
        return array(
        	// 'filterName' => 'functionName'
            'active' => 'onlyActive',
            'slug' => 'bySlug',
        );
    }

    //$parameters come from your repository, if you want add some.
    protected function byName(array $parameters)
    {
        $this->setDefaultTable('pag', $parameters);
        $this->andWhere($parameters['table'].'.name = :name');
        $this->setParameter('name', $parameters['name']);
    }

    //.....
}
```

` $this->setDefaultTable('pag', $parameters);`is  a helper to avoid hardchecking if you want apply the filter on join or directly on the main table.

Here the helper function to understand it :

```php
protected function setDefaultTable($defaultTable, array &$parameters = array())
{
    if (!isset($parameters['table'])) {
        $parameters['table'] = $defaultTable;
    }
}
```

So if you apply a filter on join, just add the table in the parameter.

**Example** :

```php
public function findMenuNode(
    $location,
    $nodePageName,
    $queryBuilderGroup = PageQueryBuilder::ENUMERABLE_QUERY_BUILDER,
    \Closure $queryBuilderConfigurator = null
)
{
    $qb = $this->createQueryBuilder($queryBuilderGroup);
    $qb->addSelect('pag_par', 'pag_par_pgc');
    $qb->leftJoin('pag.parent', 'pag_par');
    $qb->leftJoin('pag_par.pageContent', 'pag_par_pgc');
    $qb->andWhere('pag.display'.ucfirst($location).' = :location');
    $qb->applyFilter('active');
    $qb->applyFilter('name', [ 'name' => $nodePageName ]);
}
```

Use your own QueryBuilder inside repository
--------------------------------------------

Add the following method to override the createBuilder of the `EntityRepository` provided by doctrine.

```php
use Doctrine\ORM\EntityRepository;

class MyRepository extends EntityRepository
{
	/**
    * Create our pre populated query builder
    **/
    public function createQueryBuilder($group = null)
    {
        $qb = new LocaleQueryBuilder($this->getEntityManager());
        $qb->setEntityName($this->getEntityName());
        $qb->load($group);

        return $qb;
    }

    public function findSomething()
    {
    	$qb = $this->createQueryBuilder('my_group');

        //Your logic
    }
}
```
You also can use our repository :

```php
use Gos\Component\DoctrineQueryBuilder\GosEntityRepository

class MyRepository extends GosEntityRepository
{

}
```

Our repository implement : `Gos\Component\DoctrineQueryBuilder\QueryBuildableInterface`

**In the case where you extend from vendor repository and who is extend from EntityRepository, just override the `createQueryBuilder` method like above.**

####Symfony2 Integration####

In the way automatically load our repository simply add the following config in your `app/config.yml`

```yaml
doctrine:
    orm:
        default_repository_class: Gos\Component\DoctrineQueryBuilder\GosEntityRepository
```

Concret example
---------------

```php
use Gos\Component\DoctrineQueryBuilder\QueryBuilder;

class PageQueryBuilder extends QueryBuilder
{
    const ENUMERABLE_QUERY_BUILDER = 'enumerable';
    const EXPLORABLE_QUERY_BUILDER = 'explorable';

    public function registerFilters()
    {
        return array(
            'active' => 'onlyActive',
            'inactive' => 'onlyInactive',
            'slug' => 'bySlug',
            'name' => 'byName'
        );
    }

    public function configure($group)
    {
        switch ($group) {
            case self::ENUMERABLE_QUERY_BUILDER :
                $this->select('pag, pag_pgc');
                $this->from($this->getEntityName(), 'pag');
                $this->leftJoin('pag.pageContent', 'pag_pgc');
                break;
            case self::EXPLORABLE_QUERY_BUILDER :
                $this->select('pag, pag_pgc, pag_seo');
                $this->from($this->getEntityName(), 'pag');
                $this->leftJoin('pag.pageContent', 'pag_pgc');
                $this->leftJoin('pag.pageSeo', 'pag_seo');
                break;
            default:
                $this->select('pag');
                $this->from($this->getEntityName(), 'pag');
        }
    }

    protected function onlyActive(array $parameters)
    {
        $this->setDefaultTable('pag', $parameters);
        $this->andWhere($parameters['table'] . '.status = :active');
        $this->setParameter('active', ActiveStateInterface::ACTIVE_STATE);
    }

    protected function onlyInactive(array $parameters)
    {
        $this->setDefaultTable('pag', $parameters);
        $this->andWhere($parameters['table'] . '.status = :inactive');
        $this->setParameter('inactive', ActiveStateInterface::INACTIVE_STATE);
    }

    protected function bySlug(array $parameters)
    {
        $this->setDefaultTable('pag_pgc', $parameters);
        $this->andWhere($parameters['table'].'.slug = :slug');
        $this->setParameter('slug', $parameters['slugs']);
    }

    protected function byName(array $parameters)
    {
        $this->setDefaultTable('pag', $parameters);
        $this->andWhere($parameters['table'].'.name = :name');
        $this->setParameter('name', $parameters['name']);
    }
}

```

Running the tests:
------------------

PHPUnit 3.5 or newer together with Mock_Object package is required. To setup and run tests follow these steps:

* go to the root directory of fixture
* run: composer install --dev
* run: phpunit


