<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use jugger\query\LikeCriteria;
use jugger\query\LogicCriteria;
use jugger\query\EqualCriteria;
use jugger\query\RegexpCriteria;
use jugger\query\CompareCriteria;
use jugger\query\BetweenCriteria;

class CriteriaTest extends TestCase
{
    public function testName()
    {
        // WHERE (col1 = 1 AND col2 LIKE '%2%') OR (col3 > 3 AND col4 < 4)
        //
		$criteria = new LogicCriteria("or");
        $criteria->add([
            new LogicCriteria("and", [
                new EqualCriteria('col1', 1),
                new LikeCriteria('col2', '%2%'),
            ]),
            new LogicCriteria("and", [
                new RegexpCriteria('col3', '/(\d+)/'),
                new CompareCriteria('col4', '<', 4),
                new BetweenCriteria('col5', 123, 456),
            ])
        ]);
    }

    public function testLike()
    {
        $crit = new LikeCriteria("column", "%value%");
        $this->assertEquals($crit->getValue(), "%value%");
        $this->assertEquals($crit->getColumn(), "column");
    }

    public function testEqual()
    {
        $crit = new EqualCriteria("column", "%value%");
        $this->assertEquals($crit->getValue(), "%value%");
        $this->assertEquals($crit->getColumn(), "column");
    }

    public function testLogic()
    {
        $crit = new LogicCriteria("or");
        $this->assertEmpty($crit->getValue());

        $crit = new LogicCriteria("and");
        $crit->add([
            new LikeCriteria("col", "")
        ]);
        $crit->add([
            new EqualCriteria("col", "")
        ]);

        $crits = $crit->getValue();
        $this->assertEquals(count($crits), 2);
        $this->assertInstanceOf(LikeCriteria::class, $crits[0]);
        $this->assertInstanceOf(EqualCriteria::class, $crits[1]);
    }

    public function testLogicColumn()
    {
        $this->expectException(\InvalidArgumentException::class);
        $crit = new LogicCriteria("or");
        $crit->getColumn();
    }

    public function testLogicOperator()
    {
        $this->expectException(\InvalidArgumentException::class);
        $crit = new LogicCriteria("another operator");
    }

    public function testCompare()
    {
        $crit = new CompareCriteria("col", ">", 1);
        $this->assertEquals($crit->getValue(), 1);
        $this->assertEquals($crit->getColumn(), "col");
        $this->assertEquals($crit->getOperator(), ">");

        $crit = new CompareCriteria("col", ">=", 2);
        $this->assertEquals($crit->getValue(), 2);
        $this->assertEquals($crit->getColumn(), "col");
        $this->assertEquals($crit->getOperator(), ">");

        $crit = new CompareCriteria("col", "<=", 3);
        $this->assertEquals($crit->getValue(), 3);
        $this->assertEquals($crit->getColumn(), "col");
        $this->assertEquals($crit->getOperator(), ">");

        $crit = new CompareCriteria("col", "<", 4);
        $this->assertEquals($crit->getValue(), 4);
        $this->assertEquals($crit->getColumn(), "col");
        $this->assertEquals($crit->getOperator(), ">");
    }

    public function testCompareOperator()
    {
        $this->expectException(\InvalidArgumentException::class);
        $crit = new CompareCriteria("col", ">", 1);
    }

    public function testRegexp()
    {
        $crit = new RegexpCriteria("column", "%value%");
        $this->assertEquals($crit->getValue(), "%value%");
        $this->assertEquals($crit->getColumn(), "column");
    }

    public function testBetween()
    {
        $crit = new BetweenCriteria("column", "%value%");
        $this->assertEquals($crit->getValue(), "%value%");
        $this->assertEquals($crit->getColumn(), "column");
    }
}
