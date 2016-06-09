<?php

namespace Mickadoo\SearchBundle\Tests\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Mickadoo\SearchBundle\Exception\MappingNotFoundException;
use Mickadoo\SearchBundle\Service\MappingFetcher;

class MappingFetcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @throws MappingException
     */
    public function getMappingWillReturnFieldMappingIfExists()
    {
        $class = 'foo';
        $field = 'bar';

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->hasField($field)->willReturn(true);
        $metadata->getFieldMapping($field)->willReturn([])->shouldBeCalledTimes(1);
        $metadata->getAssociationMapping($field)->shouldNotBeCalled();

        $manager = $this->prophesize(EntityManager::class);
        $manager->getClassMetadata($class)->willReturn($metadata->reveal());

        $fetcher = new MappingFetcher($manager->reveal());

        $fetcher->fetch($class, $field);
    }

    /**
     * @test
     *
     * @throws MappingException
     */
    public function getMappingWillReturnAssociationMappingIfExists()
    {
        $class = 'foo';
        $field = 'bar';

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->hasField($field)->willReturn(false);
        $metadata->hasAssociation($field)->willReturn(true);
        $metadata->getAssociationMapping($field)->willReturn([])->shouldBeCalledTimes(1);
        $metadata->getFieldMapping($field)->shouldNotBeCalled();

        $manager = $this->prophesize(EntityManager::class);
        $manager->getClassMetadata($class)->willReturn($metadata->reveal());

        $fetcher = new MappingFetcher($manager->reveal());

        $fetcher->fetch($class, $field);
    }

    /**
     * @test
     *
     * @throws MappingException
     */
    public function getMappingWillThrowExceptionIfNoneExists()
    {
        $class = 'foo';
        $field = 'bar';

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->hasField($field)->willReturn(false);
        $metadata->hasAssociation($field)->willReturn(false);
        $metadata->getAssociationMapping($field)->shouldNotBeCalled();
        $metadata->getFieldMapping($field)->shouldNotBeCalled();

        $manager = $this->prophesize(EntityManager::class);
        $manager->getClassMetadata($class)->willReturn($metadata->reveal());

        $fetcher = new MappingFetcher($manager->reveal());

        $this->expectException(MappingNotFoundException::class);
        $this->expectExceptionMessage('No mapping for that field');

        $fetcher->fetch($class, $field);
    }

    /**
     * @test
     * @dataProvider getFieldsGoodProvider
     *
     * @param $fieldNames
     * @param $associationMapping
     * @param $expected
     */
    public function getFieldsWillWork($fieldNames, $associationMapping, $expected)
    {
        $class = 'foo';

        $metadata = $this->prophesize(ClassMetadata::class);
        $metadata->getFieldNames()->willReturn($fieldNames);
        $metadata->getAssociationMappings()->willReturn($associationMapping);

        $manager = $this->prophesize(EntityManager::class);
        $manager->getClassMetadata($class)->willReturn($metadata->reveal());

        $fetcher = new MappingFetcher($manager->reveal());

        $result = $fetcher->getFields($class);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getFieldsGoodProvider()
    {
        return [
            [
                ['foo'], ['bar' => 'lala'], ['foo', 'bar'],
            ],
            [
                ['foo'], ['foo' => 'lala'], ['foo'],
            ],
            [
                ['foo', 'bar'], ['foo' => 'lala'], ['foo', 'bar'],
            ],
        ];
    }
}
