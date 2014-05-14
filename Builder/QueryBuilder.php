<?php
namespace Gos\Component\DoctrineQueryBuilder\Builder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

class QueryBuilder extends DoctrineQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var array|null
     */
    private $filters = null;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @param EntityManager $em
     * @param string $entityName
     */
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);

        if ($this->filters === null) {
            $this->filters = $this->registerFilters();
        }
    }

    /**
     * @param $entityName
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param array $groups
     */
    public function load($groups = null)
    {
        if(null === $groups){
            $groups = 'default';
        }

        foreach ((array) $groups as $group) {
            $this->configure($group);
        }

        return $this;
    }

    /**
     * @param $group
     */
    protected function configure($group)
    {

    }

    /**
     * @return array
     */
    protected function registerFilters()
    {
        return array();
    }

    /**
     * @param       $filterName
     * @param array $parameters
     *
     * @return $this
     * @throws \Exception
     */
    public function applyFilter($filterName, $parameters = array())
    {
        if (array_key_exists($filterName, $this->filters)) {
            if (method_exists($this, $this->filters[$filterName])) {
                $this->{$this->filters[$filterName]}($parameters);

                return $this;
            } else {
                throw new \Exception(sprintf("Method %s not exist in class %s", $this->filters[$filterName], get_class($this)));
            }
        } else {
            throw new \Exception(sprintf("Filter %s not exist in %s", $filterName, join(', ', array_keys($this->filters))));
        }
    }

    /**
     * @param       $defaultTable
     * @param array $parameters
     */
    protected function setDefaultTable($defaultTable, array &$parameters = array())
    {
        if (!isset($parameters['table'])) {
            $parameters['table'] = $defaultTable;
        }
    }
}
