<?php

namespace jugger\query;

class RegexpCriteria extends Criteria
{
    public function __construct(string $column, string $regexp)
    {
        parent::__construct($column, $regexp);
    }
}
