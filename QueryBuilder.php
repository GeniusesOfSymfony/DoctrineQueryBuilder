<?php
namespace Gos\Component\DoctrineQueryBuilder;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

class QueryBuilder extends DoctrineQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var string
     */
    protected $entityName;

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
    public function load($groups = 'default')
    {
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
        if (!is_array($filters = $this->registerFilters())) {
            throw new \Exception(sprintf("ApplyFilter should return array, %s given", gettype($this->registerFilters())));
        }

        if (array_key_exists($filterName, $filters)) {
            if (method_exists($this, $filters[$filterName])) {
                $this->{$filters[$filterName]}($parameters);

                return $this;
            } else {
                throw new \Exception(sprintf("Method %s not exist in class %s", $filters[$filterName], get_class($this)));
            }
        } else {
            throw new \Exception(sprintf("Filter %s not exist in %s", $filterName, join(', ', array_keys($filters))));
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
