<?php

namespace jugger\query;

abstract class Criteria
{
    protected $value;
    protected $column;

    public function __construct(string $column, $value = null)
    {
        $this->value = $value;
        $this->column = $column;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getColumn(): string
    {
        return $this->column;
    }
}
