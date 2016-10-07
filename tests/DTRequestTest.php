<?php

class DTRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $dtrequest \Postitief\DBALDatatables\DTRequest
     */
    protected $dtrequest;

    protected function setUp()
    {
        $symfonyRequest = new \Symfony\Component\HttpFoundation\Request([
            'draw' => 0,
            'columns' => [
                0 => [
                    'data' => 0,
                    'name' => 'p.id',
                    'searchable' => true,
                    'orderable' => false,
                    'search' => [
                        'value' => '',
                        'regex' => false,
                    ]
                ],
                1 => [
                    'data' => 0,
                    'name' => 'p.name',
                    'searchable' => false,
                    'orderable' => false,
                    'search' => [
                        'value' => '',
                        'regex' => false,
                    ]
                ]
            ],
            'start' => 100,
            'length' => 10,
            'search' => [
                'value' => '',
                'regex' => false,
            ],
            '_' => 'anti-cache',
        ]);

        $this->dtrequest = new \Postitief\DBALDatatables\DTRequest($symfonyRequest);
    }

    public function test_get_columns()
    {
        $columns = $this->dtrequest->getColumns();
        $this->assertCount(2, $columns);
    }

    public function test_get_start()
    {
        $this->assertEquals(100, $this->dtrequest->getStart());
    }

    public function test_get_search()
    {
        $search = $this->dtrequest->getSearch();

        $this->assertFalse($search->isRegex());
        $this->assertInstanceOf(\Postitief\DBALDatatables\Search::class, $search);
    }

    public function test_get_length()
    {
        $this->assertEquals(10, $this->dtrequest->getLength());
    }

    public function test_get_draw()
    {
        $this->assertEquals(0, $this->dtrequest->getDraw());
    }

    public function test_get()
    {
        $this->assertEquals('anti-cache', $this->dtrequest->get('_'));
    }
}