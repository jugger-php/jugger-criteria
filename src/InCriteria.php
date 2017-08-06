<?php

namespace jugger\criteria;

class InCriteria extends Criteria
{
    public function __construct(string $column, $value)
    {
        parent::__construct($column, $value);
    }
}
