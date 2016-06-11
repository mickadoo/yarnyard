<?php

namespace Mickadoo\SearchBundle\Test\Service;

use Mickadoo\SearchBundle\Service\DQLPartCreator;
use Mickadoo\SearchBundle\Service\DQLValueFormatter;
use Mickadoo\SearchBundle\Util\AliasGenerator;
use Mickadoo\SearchBundle\Util\DQLNode;
use Prophecy\Argument;

class DQLPartCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider createQueryBuilderDataProvider
     *
     * @param $class
     * @param $field
     * @param $nodes
     * @param $expected
     */
    public function createWillWork($class, $field, $nodes, $expected)
    {
        $formatter = $this->prophesize(DQLValueFormatter::class);

        $formatter->format(Argument::any(), Argument::any(), Argument::any())->will(
            function ($arguments) {
                $value = $arguments[2];
                $isInt = filter_var($value, FILTER_VALIDATE_INT);

                return $isInt ? (int) $value : sprintf("'%s'", $value);
            }
        );

        $creator = new DQLPartCreator(new AliasGenerator(), $formatter->reveal());

        $result = $creator->create($nodes, $class, $field);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function createQueryBuilderDataProvider()
    {
        return [
            [
                'Laa\Foo\BarCar',
                'id',
                [
                    new DQLNode('=', 1),
                ],
                'barcar.id IN (1)',
            ],
            [
                'Laa\Foo\BarCar',
                'id',
                [
                    new DQLNode('>=', 3),
                    new DQLNode('<=', 5),
                ],
                'barcar.id >= 3 AND barcar.id <= 5',
            ],
            [
                'Foo\Bar',
                'age',
                [
                    new DQLNode('=', 1),
                    new DQLNode('>', 2),
                    new DQLNode('<', 4),
                ],
                'bar.age IN (1) OR (bar.age > 2 AND bar.age < 4)',
            ],
            [
                'Foo\Bar',
                'age',
                [
                    new DQLNode('=', 1),
                    new DQLNode('<', 2),
                    new DQLNode('>', 4),
                ],
                'bar.age IN (1) OR (bar.age < 2) OR (bar.age > 4)',
            ],
            [
                'Foo\Bar',
                'id',
                [
                    new DQLNode('=', 1),
                    new DQLNode('>', 3),
                    new DQLNode('<', 20),
                    new DQLNode('>', 16),
                    new DQLNode('<=', 10),
                    new DQLNode('=', 25),
                ],
                'bar.id IN (1,25) OR (bar.id > 3 AND bar.id <= 10) OR '
                    .'(bar.id > 16 AND bar.id < 20)',
            ],
        ];
    }
}
