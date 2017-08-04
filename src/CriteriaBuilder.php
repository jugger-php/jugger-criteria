<?php

namespace jugger\criteria;

abstract class CriteriaBuilder
{
    abstract public function build(Criteria $criteria);
}
