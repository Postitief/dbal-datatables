<?php
namespace Postitief\DBALDatatables;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class DTBuilder extends QueryBuilder
{
    /**
     * @var $request DTRequest
     */
    protected $request;

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    public function setRequest(SymfonyRequest $request)
    {
        $this->request = new DTRequest($request);

        return $this;
    }

    private function getRecordsTotal()
    {
    }

    private function getFilteredTotal()
    {

    }

    public function getData()
    {
        $columns = $this->request->getColumns();

        // get searchable columns.
        $searchableColumns = [];
        foreach($columns as $column) {
            if($column->isSearchable()) {
                $searchableColumns[$column->getName()] = $column->getSearch();
            }
        }

        // global search
        if(
            null !== $this->request->getSearch() &&
            strlen($this->request->getSearch()->getValue()) > 0
        ) {
            $value = $this->request->getSearch()->getValue();
            foreach($searchableColumns as $name => $search) {
                if (!$this->isAndWhere()) {
                    $this->where("$name LIKE :v")
                        ->setParameter('v', '%' . $value . '%');
                } else {
                    $this->orWhere("$name LIKE :v")
                        ->setParameter('v', '%' . $value . '%');
                }
            }
        }

        // the data to return.
        $data = [];
        foreach($this->execute()->fetchAll() as $record)
            $data[] =  array_values($record);
        return $data;
    }

    private function isAndWhere()
    {
        if(null !== $this->getQueryPart('where')) {
            return true;
        }

        return false;
    }

    public function getDraw()
    {
        return $this->request->get('draw', false) ? intval($this->request->get('draw')) : 0;
    }

    public function build()
    {
        return [
            'draw' => $this->getDraw(),
            'recordsTotal' => $this->getRecordsTotal(),
            'filteredTotal' => $this->getFilteredTotal(),
            'data' => $this->getData(),
        ];
    }
}