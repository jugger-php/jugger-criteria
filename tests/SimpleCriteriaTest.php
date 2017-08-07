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
use jugger\criteria\SimpleLogicCriteria;

class SimpleCriteriaTest extends TestCase
{
    public function testLogicInstance()
    {
        $crit = new SimpleLogicCriteria([]);
        $this->assertInstanceOf(LogicCriteria::class, $crit);
    }

    public function testLike()
    {
        $crit = new SimpleLogicCriteria([
            '%column1' => 'value',
            '%column2' => 'val%ue',
            '%column3' => '%value%',
        ]);
        $values = $crit->getValue();
        $this->assertTrue(count($values) == 3);

        $this->assertEquals($values[0]->getValue(), "value");
        $this->assertEquals($values[0]->getColumn(), "column1");
        $this->assertInstanceOf(LikeCriteria::class, $values[0]);

        $this->assertEquals($values[1]->getValue(), "val%ue");
        $this->assertEquals($values[1]->getColumn(), "column2");
        $this->assertInstanceOf(LikeCriteria::class, $values[1]);

        $this->assertEquals($values[2]->getValue(), "%value%");
        $this->assertEquals($values[2]->getColumn(), "column3");
        $this->assertInstanceOf(LikeCriteria::class, $values[2]);
    }

    public function testEqual()
    {
        $crit = new SimpleLogicCriteria([
            'column1' => 'value',
            '=column2' => 'value',
        ]);
        $values = $crit->getValue();
        $this->assertTrue(count($values) == 2);

        $this->assertEquals($values[0]->getValue(), "value");
        $this->assertEquals($values[0]->getColumn(), "column1");
        $this->assertInstanceOf(EqualCriteria::class, $values[0]);

        $this->assertEquals($values[1]->getValue(), "value");
        $this->assertEquals($values[1]->getColumn(), "column2");
        $this->assertInstanceOf(EqualCriteria::class, $values[1]);
    }

    public function testLogic()
    {
        $crit = new SimpleLogicCriteria([
            'or',
            'column1' => 'value',
            [
                'and',
                'column2' => 'value',
                [
                    'column3' => 'value',
                ]
            ]
        ]);
        $crit->add([
            new SimpleLogicCriteria([
                'column4' => 'value',
            ])
        ]);
        $values = $crit->getValue();

        $this->assertEquals($crit->getOperator(), "or");
        $this->assertEquals(count($values), 3);
        $this->assertEquals($values[0]->getColumn(), "column1");
        $this->assertInstanceOf(EqualCriteria::class, $values[0]);
        $this->assertInstanceOf(SimpleLogicCriteria::class, $values[1]);
        $this->assertInstanceOf(SimpleLogicCriteria::class, $values[2]);

        $childValues = $values[1]->getValue();
        $this->assertEquals($values[1]->getOperator(), "and");
        $this->assertEquals(count($childValues), 2);
        $this->assertEquals($childValues[0]->getColumn(), "column2");
        $this->assertEquals($childValues[1]->getValue()[0]->getColumn(), "column3");
        $this->assertInstanceOf(EqualCriteria::class, $childValues[0]);
        $this->assertInstanceOf(SimpleLogicCriteria::class, $childValues[1]);
        $this->assertInstanceOf(EqualCriteria::class, $childValues[1]->getValue()[0]);

        $this->assertEquals($values[2]->getValue()[0]->getColumn(), "column4");
        $this->assertInstanceOf(EqualCriteria::class, $values[2]->getValue()[0]);
    }

    public function testCompare()
    {
        $crit = new SimpleLogicCriteria([
            '>column1' => 'value',
            '>=column2' => 'value',
            '<column3' => 'value',
            '<=column4' => 'value',
            '!column5' => 'value',
            '!=column6' => 'value',
            '<>column7' => 'value',
        ]);
        $values = $crit->getValue();
        $this->assertTrue(count($values) == 7);

        $this->assertEquals($values[0]->getOperator(), ">");
        $this->assertEquals($values[0]->getValue(), "value");
        $this->assertEquals($values[0]->getColumn(), "column1");
        $this->assertInstanceOf(CompareCriteria::class, $values[0]);

        $this->assertEquals($values[1]->getOperator(), ">=");
        $this->assertEquals($values[1]->getValue(), "value");
        $this->assertEquals($values[1]->getColumn(), "column2");
        $this->assertInstanceOf(CompareCriteria::class, $values[1]);

        $this->assertEquals($values[2]->getOperator(), "<");
        $this->assertEquals($values[2]->getValue(), "value");
        $this->assertEquals($values[2]->getColumn(), "column3");
        $this->assertInstanceOf(CompareCriteria::class, $values[2]);

        $this->assertEquals($values[3]->getOperator(), "<=");
        $this->assertEquals($values[3]->getValue(), "value");
        $this->assertEquals($values[3]->getColumn(), "column4");
        $this->assertInstanceOf(CompareCriteria::class, $values[3]);

        $this->assertEquals($values[4]->getOperator(), "!="); // имено !=  (!!!)
        $this->assertEquals($values[4]->getValue(), "value");
        $this->assertEquals($values[4]->getColumn(), "column5");
        $this->assertInstanceOf(CompareCriteria::class, $values[4]);

        $this->assertEquals($values[5]->getOperator(), "!=");
        $this->assertEquals($values[5]->getValue(), "value");
        $this->assertEquals($values[5]->getColumn(), "column6");
        $this->assertInstanceOf(CompareCriteria::class, $values[5]);

        $this->assertEquals($values[6]->getOperator(), "<>");
        $this->assertEquals($values[6]->getValue(), "value");
        $this->assertEquals($values[6]->getColumn(), "column7");
        $this->assertInstanceOf(CompareCriteria::class, $values[6]);
    }

    public function testRegexp()
    {
        $crit = new SimpleLogicCriteria([
            '#column1' => "/(\d+)/",
        ]);
        $values = $crit->getValue();
        $this->assertTrue(count($values) == 1);

        $this->assertEquals($values[0]->getValue(), "/(\d+)/");
        $this->assertEquals($values[0]->getColumn(), "column1");
        $this->assertInstanceOf(RegexpCriteria::class, $values[0]);
    }

    public function testBetween()
    {
        $crit = new SimpleLogicCriteria([
            '><column1' => [100, 200],
        ]);
        $values = $crit->getValue();
        $this->assertTrue(count($values) == 1);

        $this->assertEquals($values[0]->getValue()[0], 100);
        $this->assertEquals($values[0]->getValue()[1], 200);
        $this->assertEquals($values[0]->getColumn(), "column1");
        $this->assertInstanceOf(BetweenCriteria::class, $values[0]);
    }

    public function testIn()
    {
        $crit = new SimpleLogicCriteria([
            '@column1' => [1,2,3],
            '@column2' => "string",
        ]);
        $values = $crit->getValue();
        $this->assertTrue(count($values) == 2);

        $this->assertTrue($values[0]->getValue() == [1,2,3]);
        $this->assertEquals($values[0]->getColumn(), "column1");
        $this->assertInstanceOf(InCriteria::class, $values[0]);

        $this->assertEquals($values[1]->getValue(), "string");
        $this->assertEquals($values[1]->getColumn(), "column2");
        $this->assertInstanceOf(InCriteria::class, $values[1]);
    }
}
