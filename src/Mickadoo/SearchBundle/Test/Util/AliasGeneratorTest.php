<?php

namespace Mickadoo\SearchBundle\Test\Util;

use Mickadoo\SearchBundle\Util\AliasGenerator;

class AliasGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider classProvider
     *
     * @param $class
     * @param $expected
     */
    public function generateWillWork($class, $expected)
    {
        $generator = new AliasGenerator();
        $result = $generator->generate($class);

        $this->assertEquals($expected, $result);
    }

    public function classProvider()
    {
        return [
            [
                'Foo',
                'foo',
            ],
            [
                '\\Exception',
                'exception',
            ],
            [
                'Foo\\Bar',
                'bar',
            ],
            [
                'Foo\\Bar\\AnotherClass',
                'anotherclass',
            ],
        ];
    }
}
