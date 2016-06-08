<?php

namespace Mickadoo\SearchBundle\Util;

class DQLNode
{
    /**
     * @var string
     */
    protected $operator;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param string $operator
     * @param mixed  $value
     */
    public function __construct($operator, $value)
    {
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
