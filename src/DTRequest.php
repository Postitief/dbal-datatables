<?php
namespace Postitief\DBALDatatables;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class DTRequest
{
    /**
     * @var $request SymfonyRequest
     */
    protected $request;

    /**
     * @var $columns Column[]
     */
    protected $columns;

    /**
     * @var $start int
     */
    protected $start;

    /**
     * @var $length int
     */
    protected $length;

    /**
     * @var $search Search|null
     */
    protected $search;

    /**
     * DTRequest constructor.
     *
     * @param SymfonyRequest $request
     */
    public function __construct(
        SymfonyRequest $request
    )
    {
        $this->request = $request;
    }

    /**
     * Get all the datatable columns.
     *
     * @return Column[]
     */
    public function getColumns()
    {
        $columns = $this->get('columns', []);

        $columnObjects = [];

        foreach($columns as $column) {
            $columnObjects[] = new Column($column);
        }

        return $columnObjects;
    }

    /**
     * Get the start of the datatable.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->get('start');
    }

    /**
     * Get the length of the datatable.
     *
     * @return mixed
     */
    public function getLength()
    {
        return $this->get('length');
    }

    /**
     * Get the search parameters.
     *
     * @return Search|null
     */
    public function getSearch()
    {
        $search = $this->request->get('search', null);

        if(null !== $search) {
            return new Search($search);
        }

        return $search;
    }

    /**
     * Get draw.
     *
     * @return int
     */
    public function getDraw()
    {
        return $this->get('draw', false) ? intval($this->get('draw')) : 0;
    }

    /**
     * Get a parameter from the request.
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->request->get($key, $default);
    }
}