<?php

namespace jugger\query\criteria;

abstract class CriteriaBuilder
{
    abstract public function build(Criteria $criteria);
}
