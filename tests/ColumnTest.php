<?php

class ColumnTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $column \Postitief\DBALDatatables\Column
     */
    protected $column;

    public function setUp()
    {
        $this->column = new \Postitief\DBALDatatables\Column([
            'data' => 'this_is_data',
            'name' => 'id',
            'searchable' => true,
            'orderable' => false,
            'search' => [
                'regex' => false,
                'value' => 'test_search'
            ]
        ]);
    }

    public function tearDown()
    {
        $this->column = null;
    }

    public function test_get_data()
    {
        $data = $this->column->getData();

        $this->assertEquals('this_is_data', $data);
    }

    public function test_set_data()
    {
        $expected = 'new_data';

        $actual = $this->column->setData($expected)->getData();

        $this->assertEquals($expected, $actual);
    }

    public function test_get_name()
    {
        $name = $this->column->getName();

        $this->assertEquals('id', $name);
    }

    public function test_set_name()
    {
        $expected = 'new_name';

        $actual = $this->column->setName($expected)->getName();

        $this->assertEquals($expected, $actual);
    }

    public function test_is_searchable()
    {
        $this->assertTrue($this->column->isSearchable());
    }

    public function test_set_searchable()
    {
        $expected = false;

        $actual = $this->column->setSearchable($expected)->isSearchable();

        $this->assertFalse($actual);
    }

    public function test_is_orderable()
    {
        $this->assertFalse($this->column->isOrderable());
    }

    public function test_set_orderable()
    {
        $expected = true;

        $actual = $this->column->setOrderable($expected)->isOrderable();

        $this->assertTrue($actual);
    }

    public function test_search_instance_of_search()
    {
        $this->assertInstanceOf(\Postitief\DBALDatatables\Search::class, $this->column->getSearch());
    }

    public function test_set_search()
    {
        $search = new \Postitief\DBALDatatables\Search([
            'value' => 'set_search',
            'regex' => false,
        ]);

        $expectedSearch = $this->column->setSearch($search)->getSearch();

        $this->assertFalse($expectedSearch->isRegex());
        $this->assertEquals('set_search', $expectedSearch->getValue());
    }
}