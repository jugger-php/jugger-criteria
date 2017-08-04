<?php

namespace jugger\criteria;

class LikeCriteria extends Criteria
{
    public function __construct(string $column, $value)
    {
        parent::__construct($column, $value);
    }
}
