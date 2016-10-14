<?php

namespace Tests\Mickadoo\SearchBundle\Service;

use Doctrine\DBAL\Types\Type;
use Mickadoo\SearchBundle\Service\DQLValueFormatter;
use Mickadoo\SearchBundle\Service\EntityValueValidator;

class DQLValueFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function unexpectedTypeWillThrowException()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unrecognized type');

        $class = 'foo';
        $field = 'bar';

        $validator = $this->prophesize(EntityValueValidator::class);
        $validator->getExpectedType($class, $field)->willReturn('invalid');

        $formatter = new DQLValueFormatter($validator->reveal());
        $formatter->format($class, $field, 'test');
    }

    /**
     * @test
     * @dataProvider valueProvider
     *
     * @param $value
     * @param $type
     * @param $expected
     */
    public function willReturnExpectedFormat($value, $type, $expected)
    {
        $class = 'foo';
        $field = 'bar';

        $validator = $this->prophesize(EntityValueValidator::class);
        $validator->getExpectedType($class, $field)->willReturn($type);

        $formatter = new DQLValueFormatter($validator->reveal());
        $result = $formatter->format($class, $field, $value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function valueProvider()
    {
        return [
            [
                100,
                Type::INTEGER,
                100,
            ],
            [
                '100',
                Type::INTEGER,
                100,
            ],
            [
                '100',
                Type::STRING,
                "'100'",
            ],
            [
                'i am a string too',
                Type::STRING,
                "'i am a string too'",
            ],
        ];
    }
}
