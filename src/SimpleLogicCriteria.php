<?php

namespace jugger\criteria;

class SimpleLogicCriteria extends LogicCriteria
{
    public function __construct(array $criterias)
    {
        $operator = "and";
        if (isset($criterias[0]) && is_scalar($criterias[0])) {
            $operator = strtolower($criterias[0]) == 'or' ? 'or' : 'and';
            unset($criterias[0]);
        }

        $newCriterias = [];
        foreach ($criterias as $key => $value) {
            if (is_integer($key) && is_array($value)) {
                $newCriterias[] = new SimpleLogicCriteria($value);
            }
            elseif (is_integer($key)) {
                throw new \Exception("Invalide value criteria ". var_export($value, true));
            }
            else {
                $operator = $this->parseOperatorFromKey($key);
                $newCriterias[] = $this->createCriteriaFromOperator($operator, $value);
            }
        }
        parent::__construct($operator, $newCriterias);
    }

    public function parseOperatorFromKey(string $key)
    {
        $operators = [
            '><', '>=', '<=', '!=', '<>', '<', '!', '>', '=', '@', '%',
        ];
        $operators = join($operators, "|");
        $regexp = "/^({$operators})(.+)$/";
        if (preg_match($regexp, $key, $m)) {
            return $m[0];
        }
        else {
            return "=";
        }
    }

    public function createCriteriaFromOperator(string $operator, string $column, $value): Criteria
    {
        switch ($operator) {
            case '=':
                return new EqualCriteria($column, $value);

            case '@':
                return new InCriteria($column, $value);

            case '%':
                return new LikeCriteria($column, $value);

            case '><':
                return new BetweenCriteria($column, $value[0], $value[1]);

            case '>=':
            case '>':
            case '<=':
            case '<':
            case '<>':
                return new CompareCriteria($operator, $column, $value);

            case '!':
                return new CompareCriteria("!=", $column, $value);

            default:
                throw new \Exception("Not found operator '{$operator}' for column '{$column}'");
        }
    }
}
