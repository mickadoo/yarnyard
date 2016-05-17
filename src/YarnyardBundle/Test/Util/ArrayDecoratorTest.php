<?php

namespace YarnyardBundle\Test\Util;

use YarnyardBundle\Util\ArrayDecorator;

class ArrayDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider arrayProvider
     *
     * @param array $array
     */
    public function allArrayKeysWillBeSurroundedByPercentageSigns($array)
    {
        $decorator = new ArrayDecorator();
        $result = $decorator->decorateKeys($array);

        foreach (array_keys($result) as $key) {
            $charArray = str_split($key);
            $this->assertEquals('%', $charArray[0]);
            $this->assertEquals('%', end($charArray));
        }
    }

    public function arrayProvider()
    {
        return [
            [
                [
                    'a' => 'b'
                ]
            ],
            [
                [
                    'a key with spaces' => 'b'
                ]
            ],

            [
                [
                    'a key with spaces' => 'b',
                    'second key' => 'blah'
                ]
            ],
        ];
    }
}
