<?php
namespace Postitief\DBALDatatables;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class DTRequest
{
    protected $request;

    protected $columns;

    protected $search;

    public function __construct(
        SymfonyRequest $request
    )
    {
        $this->request = $request;
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        $columns = $this->request->get('columns', []);

        $columnObjects = [];

        foreach($columns as $column) {
            $columnObjects[] = new Column($column);
        }

        return $columnObjects;
    }

    public function getSearch()
    {
        $search = $this->request->get('search', null);

        if(null !== $search) {
            return new Search($search);
        }

        return $search;
    }

    public function get($key, $default = null)
    {
        $allParameters = array_merge($this->request->request->all(), $this->request->query->all());

        if(isset($allParameters[$key])) {
            return $key;
        }

        return $default;
    }
}