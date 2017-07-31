<?php

namespace jugger\query\criteria;

class CompareCriteria extends Criteria
{
    protected $operator;

    public function __construct(string $column, string $operator, $value)
    {
        $operators = ['>', '>=', '<', '<=', '<>', '!='];
        if (in_array($operator, $operators)) {
            $this->operator = $operator;
        }
        else {
            throw new \InvalidArgumentException("Operator must be '>', '>=', '<', '<=', '<>', '!='");
        }
        parent::__construct($column, $value);
    }

    public function getOperator(): string
    {
        return $this->operator;
    }
}
