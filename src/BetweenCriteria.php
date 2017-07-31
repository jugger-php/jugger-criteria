<?php

namespace jugger\query;

class BetweenCriteria extends Criteria
{
    protected $min;
    protected $max;

    public function __construct(string $column, float $min, float $max)
    {
        $this->min = $min;
        $this->max = $max;
        $this->column = $column;
    }

    public function getValue()
    {
        return [
            $this->getMin(),
            $this->getMax(),
        ];
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
