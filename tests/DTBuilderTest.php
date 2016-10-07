<?php

use Mockery as m;

class DTBuilderTest extends PHPUnit_Framework_TestCase
{
    protected $dtbuilder;

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

        $connection = m::mock(\Doctrine\DBAL\Connection::class);
//        $connection->shouldReceive('exectureQuery')->

        $this->dtbuilder = new \Postitief\DBALDatatables\DTBuilder($connection);
        $this->dtbuilder->setRequest($symfonyRequest);
    }

    public function test_set_request()
    {
        $request = $this->dtbuilder->getRequest();

//        $this->assertInstanceOf(\Postitief\DBALDatatables\DTRequest::class, $request);
    }

    public function test_get_records_total()
    {
//        $this->assertEquals(2, $this->dtbuilder->getRecordsTotal());
    }
}