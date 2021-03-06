<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use jugger\criteria\InCriteria;
use jugger\criteria\LikeCriteria;
use jugger\criteria\LogicCriteria;
use jugger\criteria\EqualCriteria;
use jugger\criteria\RegexpCriteria;
use jugger\criteria\CompareCriteria;
use jugger\criteria\BetweenCriteria;

class CriteriaTest extends TestCase
{
    public function testGeneral()
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
        $this->expectException(\Exception::class);
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
        $this->assertEquals($crit->getOperator(), ">=");

        $crit = new CompareCriteria("col", "<=", 3);
        $this->assertEquals($crit->getValue(), 3);
        $this->assertEquals($crit->getColumn(), "col");
        $this->assertEquals($crit->getOperator(), "<=");

        $crit = new CompareCriteria("col", "<", 4);
        $this->assertEquals($crit->getValue(), 4);
        $this->assertEquals($crit->getColumn(), "col");
        $this->assertEquals($crit->getOperator(), "<");
    }

    public function testCompareOperator()
    {
        $this->expectException(\InvalidArgumentException::class);
        $crit = new CompareCriteria("col", "><", 1);
    }

    public function testRegexp()
    {
        $crit = new RegexpCriteria("column", "/(\d+)/");
        $this->assertEquals($crit->getValue(), "/(\d+)/");
        $this->assertEquals($crit->getColumn(), "column");
    }

    public function testBetween()
    {
        $crit = new BetweenCriteria("column", 10, 20);
        $value = $crit->getValue();
        $this->assertEquals($crit->getMin(), 10);
        $this->assertEquals($crit->getMin(), $value[0]);
        $this->assertEquals($crit->getMax(), 20);
        $this->assertEquals($crit->getMax(), $value[1]);
        $this->assertEquals($crit->getColumn(), "column");
    }

    public function testIn()
    {
        $crit = new InCriteria("column", "string");
        $this->assertEquals($crit->getValue(), "string");
        $this->assertEquals($crit->getColumn(), "column");

        $crit = new InCriteria("column", [1,2,3]);
        $this->assertTrue($crit->getValue() == [1,2,3]);
        $this->assertEquals($crit->getColumn(), "column");
    }
}
