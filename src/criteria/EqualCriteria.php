<?php

namespace jugger\query\criteria;

class EqualCriteria extends Criteria
{
    public function __construct(string $column, $value)
    {
        parent::__construct($column, $value);
    }
}
