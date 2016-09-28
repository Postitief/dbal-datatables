<?php
namespace Postitief\DBALDatatables;

class Column
{
    /**
     * @var $data string
     */
    protected $data;

    /**
     * @var $name string
     */
    protected $name;

    /**
     * @var $searchable bool
     */
    protected $searchable;

    /**
     * Is this column orderable.
     *
     * @var $orderable bool
     */
    protected $orderable;

    /**
     * @var $search Search
     */
    protected $search;

    /**
     * Column constructor.
     *
     * @param array $column
     */
    public function __construct(array $column)
    {
        $this->data = isset($column['data']) ? (string)$column['data'] : '';
        $this->name = isset($column['name']) ? (string)$column['name'] : '';
        $this->searchable = (isset($column['searchable']) && $column['searchable'] == "true") ? true : false;
        $this->orderable = (isset($column['orderable']) && $column['orderable'] == "true") ? true : false;
        $this->search = isset($column['search']) ? new Search($column['search']) : null;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return Column
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Column
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @param boolean $searchable
     * @return Column
     */
    public function setSearchable($searchable)
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOrderable()
    {
        return $this->orderable;
    }

    /**
     * @param boolean $orderable
     * @return Column
     */
    public function setOrderable($orderable)
    {
        $this->orderable = $orderable;
        return $this;
    }

    /**
     * @return Search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param Search $search
     * @return Column
     */
    public function setSearch($search)
    {
        $this->search = $search;
        return $this;
    }
}