<?php

namespace Mickadoo\SearchBundle\Test\Util;

use Mickadoo\SearchBundle\Util\DQLNode;
use Mickadoo\SearchBundle\Util\PropertyParser;

class PropertyParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider parseDataProvider
     *
     * @param $string
     * @param $expected
     */
    public function parseWillWork($string, $expected)
    {
        $parser = new PropertyParser();
        $result = $parser->parse($string);

        $this->assertEquals($expected, $result);
    }

    public function parseDataProvider()
    {
        return [
            [
                '1',
                [
                    new DQLNode('=', 1),
                ],
            ],
            [
                '>2,<4',
                [
                    new DQLNode('>', 2),
                    new DQLNode('<', 4),
                ],
            ],
            [
                '1,>=2,<=4,>4333,5',
                [
                    new DQLNode('=', 1),
                    new DQLNode('>=', 2),
                    new DQLNode('<=', 4),
                    new DQLNode('>', 4333),
                    new DQLNode('=', 5),
                ],
            ],
        ];
    }
}
