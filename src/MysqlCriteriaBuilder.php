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
        $criteriaClass = get_class($criteria);
        switch ($criteriaClass) {
            case BetweenCriteria::class:
                return $this->buildBetween($criteria);

            case CompareCriteria::class:
                return $this->buildCompare($criteria);

            case EqualCriteria::class:
                return $this->buildEqual($criteria);

            case LikeCriteria::class:
                return $this->buildLike($criteria);

            case LogicCriteria::class:
                return $this->buildLogic($criteria);

            case RegexpCriteria::class:
                return $this->buildRegexp($criteria);

            default:
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

    protected function buildWithOperator($column, $operator, $value)
    {
        $value = $this->escape($value);
        $column = $this->buildColumn($column);
        return "{$column} {$operator} '{$value}'";
    }
}
