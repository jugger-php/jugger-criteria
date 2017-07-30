<?php

namespace jugger\query;

class BetweenCriteria extends Criteria
{
    protected $min;
    protected $max;

    public function __construct(string $column, float $min, float $max)
    {
        parent::__construct($column);
        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function getMax(): float
    {
        return $this->max;
    }
}
