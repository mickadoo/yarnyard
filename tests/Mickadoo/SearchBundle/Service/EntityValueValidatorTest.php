<?php

namespace Tests\Mickadoo\SearchBundle\Service;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mickadoo\SearchBundle\Service\EntityValueValidator;
use Mickadoo\SearchBundle\Service\MappingFetcher;

class EntityValueValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider getTypeGoodProvider
     *
     * @param $mapping
     * @param $expected
     *
     * @throws \Exception
     */
    public function getExpectedTypeWillWork($mapping, $expected)
    {
        $class = 'foo';
        $field = 'bar';

        $fetcher = $this->prophesize(MappingFetcher::class);
        $fetcher->fetch($class, $field)->willReturn($mapping);

        $validator = new EntityValueValidator($fetcher->reveal());

        $result = $validator->getExpectedType($class, $field);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider isValidGoodProvider
     *
     * @param $value
     * @param $mappingType
     * @param $expected
     */
    public function isValidWillWork($value, $mappingType, $expected)
    {
        $class = 'foo';
        $field = 'bar';
        $mapping = ['type' => $mappingType];

        $fetcher = $this->prophesize(MappingFetcher::class);
        $fetcher->fetch($class, $field)->willReturn($mapping);
        $fetcher->getFields($class)->willReturn([$field]);

        $validator = new EntityValueValidator($fetcher->reveal());

        $result = $validator->isValid($class, $field, $value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function willNotBeValidIfFieldDoesNotExist()
    {
        $class = 'foo';
        $field = 'bar';

        $fetcher = $this->prophesize(MappingFetcher::class);
        $fetcher->getFields($class)->willReturn([]);

        $validator = new EntityValueValidator($fetcher->reveal());

        $result = $validator->isValid($class, $field, 'lalaa');

        $this->assertEquals(false, $result);
    }

    /**
     * @test
     *
     * @throws \Exception
     */
    public function getExpectedTypeWillFailWithInvalidType()
    {
        $class = 'foo';
        $field = 'bar';
        $mapping = ['type' => 'invalid'];

        $fetcher = $this->prophesize(MappingFetcher::class);
        $fetcher->fetch($class, $field)->willReturn($mapping);
        $fetcher->getFields($class)->willReturn([$field]);

        $validator = new EntityValueValidator($fetcher->reveal());

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('unrecognized mapping type');

        $validator->getExpectedType($class, $field);
    }

    /**
     * @return array
     */
    public function isValidGoodProvider()
    {
        return [
            [
                1,
                'integer',
                true,
            ],
            [
                1,
                'string',
                false,
            ],
            [
                'abc',
                'string',
                true,
            ],
            [
                'abc',
                'integer',
                false,
            ],
            [
                '1',
                ClassMetadata::ONE_TO_MANY,
                true,
            ],
            [
                'abc',
                ClassMetadata::ONE_TO_MANY,
                false,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getTypeGoodProvider()
    {
        return [
            [
                ['type' => 'integer'], Type::INTEGER,
            ],
            [
                ['type' => ClassMetadata::ONE_TO_MANY], Type::INTEGER,
            ],
            [
                ['type' => ClassMetadata::MANY_TO_MANY], Type::INTEGER,
            ],
            [
                ['type' => ClassMetadata::MANY_TO_ONE], Type::INTEGER,
            ],
            [
                ['type' => ClassMetadata::ONE_TO_ONE], Type::INTEGER,
            ],
            [
                ['type' => 'string'], Type::STRING,
            ],
        ];
    }
}
