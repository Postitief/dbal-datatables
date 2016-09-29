<?php
namespace Postitief\DBALDatatables;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class DTBuilder
 *
 * @link https://datatables.net/manual/server-side#Returned-data
 * @package Postitief\DBALDatatables
 */
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

        $dtb->setFirstResult(null)
            ->setMaxResults(null);

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
    private function getRecordsFilteredTotal()
    {
        $dtb = clone($this);

        $dtb->setFirstResult(null)
            ->setMaxResults(null);

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
    private function getData()
    {
        $columns = $this->request->getColumns();

        // get searchable columns.
        $searchableColumns = $this->getSearchableColumns($columns);

        // Create search based on all columns.
        $this->createSearchableQuery($columns);

        // global search
        $this->createGlobalSearchQuery($searchableColumns);

        // the data to return.
        $data = [];

        // Create stmt.
        $stmt = $this
            ->setFirstResult($this->request->getStart())
            ->setMaxResults($this->request->getLength())
            ->execute();

        foreach($stmt->fetchAll(\PDO::FETCH_ASSOC) as $record) {
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
     * Add all where statements based on all the columns.
     *
     * @param $columns
     * @return array
     */
    protected function createSearchableQuery($columns)
    {
        $i = 0;
        foreach ($columns as $column)
        {
            if (
                $column->isSearchable() &&
                strlen($column->getSearch()->getValue()) > 0
            ) {
                $value = $column->getSearch()->getValue();
                $name = $column->getName();

                /**
                 * if value contains >= or <= make an exception
                 */
                if($value[0] == '%' && $value[strlen($value) - 1] == '%') {
                    $value = substr($value, 1, strlen($value) - 2);

                    if(!$this->isAndWhere()) {
                        $this->where("$name LIKE :val$i")
                            ->setParameter('val' . $i, '%' . $value . '%');
                    } else {
                        $this->andWhere("$name LIKE :val$i")
                            ->setParameter('val' . $i, '%' . $value . '%');
                    }

                    $i++;
                    continue;
                }

                if(strpos($value, '&&') !== false) {
                    $wheres = explode('&&', $value);

                    foreach($wheres as $key => $where) {
                        $wheres[$key] = substr($where, 2, strlen($where));
                    }

                    if(!$this->isAndWhere()) {
                        $this->where("$name >= :val$i")
                            ->setParameter('val' . $i, $wheres[0]);
                    } else {
                        $this->andWhere("$name >= :val$i")
                            ->setParameter('val' . $i, $wheres[0]);
                    }

                    if(!$this->isAndWhere()) {
                        $this->where("$name <= :valste$i")
                            ->setParameter('valste' . $i, $wheres[1]);
                    } else {
                        $this->andWhere("$name <= :valste$i")
                            ->setParameter('valste' . $i, $wheres[1]);
                    }

                    $i++;
                    continue;
                }

                if(strpos($value, '>=') !== false) {
                    $value = substr($value, 2, strlen($value));
                    if (!$this->isAndWhere()) {
                        $this->where("$name >= :val$i")
                            ->setParameter('val' . $i, $value);
                    } else {
                        $this->andWhere("$name >= :val$i")
                            ->setParameter('val' . $i, $value);
                    }
                    $i++;
                    continue;
                } elseif(strpos($value, '<=') !== false) {
                    $value = substr($value, 2, strlen($value));
                    if (!$this->isAndWhere()) {
                        $this->where("$name <= :val$i")
                            ->setParameter('val' . $i, $value);
                    } else {
                        $this->andWhere("$name <= :val$i")
                            ->setParameter('val' . $i, $value);
                    }
                    $i++;
                    continue;
                }

                if (!$this->isAndWhere()) {
                    $this->where("$name = :val$i")
                        ->setParameter('val' . $i, $value);
                } else {
                    $this->andWhere("$name = :val$i")
                        ->setParameter('val' . $i, $value);
                }
                $i++;
            }
        }
    }

    /**
     * Create the global search query.
     *
     * @param $searchableColumns
     */
    protected function createGlobalSearchQuery($searchableColumns)
    {
        $i = 0;
        if (
            null !== $this->request->getSearch() &&
            strlen($this->request->getSearch()->getValue()) > 0
        ) {
            foreach ($searchableColumns as $name => $search) {
                $value = $this->request->getSearch()->getValue();
                if (!$this->isAndWhere()) {
                    $this->where("$name LIKE :v$i")
                        ->setParameter('v' . $i, '%' . $value . '%');
                } else {
                    $this->orWhere("$name LIKE :v")
                        ->setParameter('v' . $i, '%' . $value . '%');
                }
                $i++;
            }
        }
    }

    /**
     * Get all the columns where we can search on.
     *
     * @param $columns
     * @return array
     */
    private function getSearchableColumns($columns)
    {
        $searchableColumns = [];
        foreach ($columns as $column) {
            if ($column->isSearchable()) {
                $searchableColumns[$column->getName()] = $column->getSearch();
            }
        }
        return $searchableColumns;
    }

    /**
     * Build the datatable output.
     *
     * @return array
     */
    public function build()
    {
        // First execute the getData to be sure the where clauses of this query builder are filled.
        // Then we can calculate the recordsTotal and the recordsFiltered.
        $data = $this->getData();

        return [
            'draw' => $this->request->getDraw(),
            'recordsTotal' => $this->getRecordsTotal(),
            'recordsFiltered' => $this->getRecordsFilteredTotal(),
            'data' => $data,
        ];
    }
}