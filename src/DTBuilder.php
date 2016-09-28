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

    /**
     * DTBuilder constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection);
    }

    /**
     * Set the request.
     *
     * @param SymfonyRequest $request
     * @return $this
     */
    public function setRequest(SymfonyRequest $request)
    {
        $this->request = new DTRequest($request);

        return $this;
    }

    /**
     * Get the total records.
     *
     * @return int
     */
    private function getRecordsTotal()
    {
        $dtb = clone($this);

        $stmt = $dtb->resetQueryParts(['where', 'select'])
                    ->select("COUNT(*) as recordsTotal")
                    ->execute();

        $result = $stmt->fetch();

        $recordsTotal = $result['recordsTotal'];

        unset($dtb);

        return intval($recordsTotal);
    }

    /**
     * Get the filtered records.
     *
     * @return int
     */
    private function getFilteredTotal()
    {
        $dtb = clone($this);

        $stmt = $dtb->resetQueryParts(['select'])
            ->select("COUNT(*) as filteredTotal")
            ->execute();

        $result = $stmt->fetch();

        $filteredTotal = $result['filteredTotal'];

        unset($dtb);

        return intval($filteredTotal);
    }

    /**
     * Build the query and execute it with the datatable request
     * and return the rows.
     *
     * @return array
     */
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

        foreach($columns as $column) {
            if(
            $column->isSearchable() &&
            strlen($column->getSearch()->getValue()) > 0
            ) {
                $value = $column->getSearch()->getValue();
                $name = $column->getName();
                if(!$this->isAndWhere()) {
                    $this->where("$name = :val")
                        ->setParameter('val', $value);
                } else {
                    $this->andWhere("$name = :val")
                        ->setParameter('val', $value);
                }
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

        // Create stmt.
        $stmt = $this
            ->setFirstResult($this->request->getStart())
            ->setMaxResults($this->request->getLength())
            ->execute();

        foreach($stmt->fetchAll() as $record) {
            $data[] = array_values($record);
        }

        return $data;
    }

    /**
     * If we already have a where we should use and where
     *
     * @return bool
     */
    private function isAndWhere()
    {
        if(null !== $this->getQueryPart('where')) {
            return true;
        }

        return false;
    }

    /**
     * Get draw.
     *
     * @return int
     */
    public function getDraw()
    {
        return $this->request->get('draw', false) ? intval($this->request->get('draw')) : 0;
    }

    /**
     * Build the datatable output.
     *
     * @return array
     */
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