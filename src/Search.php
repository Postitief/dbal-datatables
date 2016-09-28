<?php
namespace Postitief\DBALDatatables;

class Search
{
    /**
     * The value to search for.
     *
     * @var $value string
     */
    protected $value;

    /**
     * Is the value a regex.
     *
     * @var $regex bool
     */
    protected $regex;

    /**
     * Search constructor.
     *
     * @param $search
     */
    public function __construct($search)
    {
        $this->value = isset($search['value']) ? (string)$search['value'] : "";
        $this->regex = (isset($column['regex']) && $column['regex'] == "true") ? true : false;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Search
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRegex()
    {
        return $this->regex;
    }

    /**
     * @param boolean $regex
     * @return Search
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
        return $this;
    }
}