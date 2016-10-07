<?php

class SearchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $search \Postitief\DBALDatatables\Search
     */
    protected $search;

    protected function setUp()
    {
        $this->search = new \Postitief\DBALDatatables\Search([
            'value' => 'search_test',
            'regex' => false,
        ]);
    }

    public function tearDown()
    {
        $this->search = null;
    }

    public function test_get_value()
    {
        $value = $this->search->getValue();

        $this->assertEquals('search_test', $value);
    }

    public function test_set_value()
    {
        $value = 'new_value';

        $this->search->setValue($value);

        $this->assertEquals($value, $this->search->getValue());
    }

    public function test_is_regex()
    {
        $regex = $this->search->isRegex();

        $this->assertFalse($regex);
    }

    public function test_set_regex()
    {
        $regex = true;

        $this->search->setRegex($regex);

        $this->assertTrue($this->search->isRegex());
    }
}