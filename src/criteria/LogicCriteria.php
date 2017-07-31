<?php

namespace jugger\query\criteria;

class LogicCriteria extends Criteria
{
    protected $operator;

    public function __construct(string $operator, array $value = [])
    {
        if (in_array($operator, ['or', 'and'])) {
            $this->operator = $operator;
        }
        else {
            throw new \InvalidArgumentException("Operator must be 'or' or 'and'");
        }
        $this->add($value);
    }

    public function add(array $criteries)
    {
        foreach ($criteries as $item) {
            if ($item instanceof Criteria) {
                $this->value[] = $item;
            }
            else {
                throw new \InvalidArgumentException("Addtion item must be extends \\jugger\\query\\Criteria");
            }
        }
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getColumn(): string
    {
        throw new \Exception("LogicCriteria not have column");
    }
}
