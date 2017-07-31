<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use jugger\query\criteria\MysqlCriteriaBuilder;
use jugger\query\criteria\LikeCriteria;
use jugger\query\criteria\LogicCriteria;
use jugger\query\criteria\EqualCriteria;
use jugger\query\criteria\RegexpCriteria;
use jugger\query\criteria\CompareCriteria;
use jugger\query\criteria\BetweenCriteria;

class MysqlCriteriaBuilderTest extends TestCase
{
    public function testGeneral()
    {
		$criteria = new LogicCriteria("or");
        $criteria->add([
            new LogicCriteria("and", [
                new EqualCriteria('col1', 1),
                new LikeCriteria('col2', '%2%'),
            ]),
            new LogicCriteria("and", [
                new RegexpCriteria('col3', '(\d+)'),
                new CompareCriteria('col4', '<', 4),
                new BetweenCriteria('col5', 123, 456),
            ])
        ]);
        $builder = new MysqlCriteriaBuilder();
        $sql = $builder->build($criteria);
        $this->assertEquals(
            $sql,
            "((`col1` = '1') AND (`col2` LIKE '%2%')) OR ((`col3` REGEXP '(\d+)') AND (`col4` < '4') AND (`col5` BETWEEN '123' AND '456'))"
        );
    }

    public function testLike()
    {
        $crit = new LikeCriteria("col", "%value%");
        $builder = new MysqlCriteriaBuilder();
        $this->assertEquals(
            $builder->build($crit),
            "`col` LIKE '%value%'"
        );
        $this->assertEquals(
            $builder->buildLike($crit),
            $builder->build($crit)
        );
    }

    public function testEqual()
    {
        $crit = new EqualCriteria("col", "%value%");
        $builder = new MysqlCriteriaBuilder();
        $this->assertEquals(
            $builder->build($crit),
            "`col` = '%value%'"
        );
        $this->assertEquals(
            $builder->buildEqual($crit),
            $builder->build($crit)
        );
    }

    public function testLogic()
    {
        $crit = new LogicCriteria("and");
        $crit->add([
            new LikeCriteria("col", "")
        ]);
        $crit->add([
            new EqualCriteria("col", "")
        ]);

        $builder = new MysqlCriteriaBuilder();
        $this->assertEquals(
            $builder->build($crit),
            "(`col` LIKE '') AND (`col` = '')"
        );
        $this->assertEquals(
            $builder->buildLogic($crit),
            $builder->build($crit)
        );
    }

    public function testCompare()
    {
        $crit = new CompareCriteria("col", ">", 1);
        $builder = new MysqlCriteriaBuilder();
        $this->assertEquals(
            $builder->build($crit),
            "`col` > '1'"
        );
        $this->assertEquals(
            $builder->buildCompare($crit),
            $builder->build($crit)
        );
    }

    public function testRegexp()
    {
        $crit = new RegexpCriteria("col", "/(\d+)/");
        $builder = new MysqlCriteriaBuilder();
        $this->assertEquals(
            $builder->build($crit),
            "`col` REGEXP '/(\d+)/'"
        );
        $this->assertEquals(
            $builder->buildRegexp($crit),
            $builder->build($crit)
        );
    }

    public function testBetween()
    {
        $crit = new BetweenCriteria("col", 10, 20);
        $builder = new MysqlCriteriaBuilder();
        $this->assertEquals(
            $builder->build($crit),
            "`col` BETWEEN '10' AND '20'"
        );
        $this->assertEquals(
            $builder->buildBetween($crit),
            $builder->build($crit)
        );
    }
}
