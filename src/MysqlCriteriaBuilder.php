<?php

namespace jugger\criteria;

class MysqlCriteriaBuilder extends CriteriaBuilder
{
    protected $driver;

    public function __construct(\mysqli $driver)
    {
        $this->driver = $driver;
    }

    public function build(Criteria $criteria): string
    {
        if ($criteria instanceof BetweenCriteria) {
            return $this->buildBetween($criteria);
        }
        else if ($criteria instanceof CompareCriteria) {
            return $this->buildCompare($criteria);
        }
        else if ($criteria instanceof EqualCriteria) {
            return $this->buildEqual($criteria);
        }
        else if ($criteria instanceof LikeCriteria) {
            return $this->buildLike($criteria);
        }
        else if ($criteria instanceof LogicCriteria) {
            return $this->buildLogic($criteria);
        }
        else if ($criteria instanceof InCriteria) {
            return $this->buildIn($criteria);
        }
        else if ($criteria instanceof RegexpCriteria) {
            return $this->buildRegexp($criteria);
        }
        else {
            $criteriaClass = get_class($criteria);
            throw new \Exception("Not found class of criteria as '{$criteriaClass}'");
        }
    }

    public function buildColumn(string $name)
    {
        return "`{$name}`";
    }

    public function escape(string $value)
    {
        return $this->driver->real_escape_string($value);
    }

    public function buildLogic(LogicCriteria $criteria)
    {
        $operands = [];
        $operator = strtoupper($criteria->getOperator());
        foreach ($criteria->getValue() as $item) {
            $sql = $this->build($item);
            $operands[] = "({$sql})";
        }
        return join($operands, " {$operator} ");
    }

    public function buildBetween(BetweenCriteria $criteria)
    {
        $column = $this->buildColumn(
            $criteria->getColumn()
        );
        $min = (float) $criteria->getMin();
        $max = (float) $criteria->getMax();

        return "{$column} BETWEEN '{$min}' AND '{$max}'";
    }

    public function buildCompare(CompareCriteria $criteria)
    {
        return $this->buildWithOperator(
            $criteria->getColumn(),
            $criteria->getOperator(),
            $criteria->getValue()
        );
    }

    public function buildEqual(EqualCriteria $criteria)
    {
        return $this->buildWithOperator(
            $criteria->getColumn(),
            "=",
            $criteria->getValue()
        );
    }

    public function buildLike(LikeCriteria $criteria)
    {
        return $this->buildWithOperator(
            $criteria->getColumn(),
            "LIKE",
            $criteria->getValue()
        );
    }

    public function buildRegexp(RegexpCriteria $criteria)
    {
        return $this->buildWithOperator(
            $criteria->getColumn(),
            "REGEXP",
            $criteria->getValue()
        );
    }

    public function buildIn(InCriteria $criteria)
    {
        $column = $this->buildColumn(
            $criteria->getColumn()
        );
        $value = $criteria->getValue();
        if (is_array($value)) {
            $value = join(
                ", ",
                array_map([$this, 'escape'], $value)
            );
        }
        else {
            $value = $this->escape($value);
        }
        return "{$column} IN ({$value})";
    }

    protected function buildWithOperator($column, $operator, $value)
    {
        $value = $this->escape($value);
        $column = $this->buildColumn($column);
        return "{$column} {$operator} '{$value}'";
    }
}
