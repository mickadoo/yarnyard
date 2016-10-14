<?php

namespace Tests\Mickadoo\SearchBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mickadoo\SearchBundle\Service\DQLPartCreator;
use Mickadoo\SearchBundle\Service\EntityFinder;
use Mickadoo\SearchBundle\Service\EntityValueValidator;
use Mickadoo\SearchBundle\Util\AliasGenerator;
use Mickadoo\SearchBundle\Util\PropertyParser;
use Prophecy\Argument as Arg;
use Prophecy\Prophecy\ObjectProphecy;

class EntityFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function basicHappyCaseWillWork()
    {
        $class = 'Foo';
        $alias = 'foo';

        $finder = $this->getFinder();
        $repo = $this->getRepo($class, $alias);

        $result = $finder->createQueryBuilder(
            $repo->reveal(),
            ['prop1' => 'val', 'prop2' => 'val']
        );

        $this->assertEquals(
            sprintf('SELECT FROM %s %s WHERE PART AND PART', $class, $alias),
            $result->getDQL()
        );
    }

    /**
     * @return EntityFinder
     */
    private function getFinder()
    {
        $validator = $this->prophesize(EntityValueValidator::class);
        $validator->isValid(Arg::any(), Arg::any(), Arg::any())->willReturn(true);
        $partCreator = $this->prophesize(DQLPartCreator::class);
        $partCreator->create(Arg::any(), Arg::any(), Arg::any())->willReturn('PART');

        $finder = new EntityFinder(
            new PropertyParser(),
            $validator->reveal(),
            $partCreator->reveal(),
            new AliasGenerator()
        );

        return $finder;
    }

    /**
     * @param $class
     * @param $alias
     *
     * @return EntityRepository|ObjectProphecy
     */
    private function getRepo($class, $alias)
    {
        $manager = $this->prophesize(EntityManager::class);
        $queryBuilder = new QueryBuilder($manager->reveal());
        $queryBuilder->from($class, $alias);

        $repo = $this->prophesize(EntityRepository::class);
        $repo->getClassName()->willReturn($class);
        $repo->createQueryBuilder($alias)->willReturn($queryBuilder);

        return $repo;
    }

    /**
     * @test
     */
    public function noParamsWillGenerateBasicQuery()
    {
        $class = 'Foo';
        $alias = 'foo';

        $finder = $this->getFinder();
        $repo = $this->getRepo($class, $alias);

        $result = $finder->createQueryBuilder($repo->reveal(), []);

        $this->assertEquals(
            sprintf('SELECT FROM %s %s', $class, $alias),
            $result->getDQL()
        );
    }

    /**
     * @test
     */
    public function idSuffixWillBeRemoved()
    {
        $class = 'Foo';
        $alias = 'foo';
        $property = 'propId';
        $withoutSuffix = 'prop';

        $validator = $this->prophesize(EntityValueValidator::class);
        $validator
            ->isValid(Arg::any(), $withoutSuffix, Arg::any())
            ->shouldBeCalled()
            ->willReturn(true);

        $partCreator = $this->prophesize(DQLPartCreator::class);
        $partCreator->create(Arg::any(), Arg::any(), Arg::any())->willReturn('PART');

        $finder = new EntityFinder(
            new PropertyParser(),
            $validator->reveal(),
            $partCreator->reveal(),
            new AliasGenerator()
        );

        $repo = $this->getRepo($class, $alias);
        $finder->createQueryBuilder($repo->reveal(), [$property => 'val']);
    }

    /**
     * @test
     */
    public function invalidPropertiesWillBeIgnored()
    {
        $class = 'Foo';
        $alias = 'foo';

        $validator = $this->prophesize(EntityValueValidator::class);
        $validator->isValid(Arg::any(), Arg::any(), Arg::any())->willReturn(false);
        $partCreator = $this->prophesize(DQLPartCreator::class);
        $partCreator->create(Arg::any(), Arg::any(), Arg::any())->shouldNotBeCalled();

        $finder = new EntityFinder(
            new PropertyParser(),
            $validator->reveal(),
            $partCreator->reveal(),
            new AliasGenerator()
        );

        $repo = $this->getRepo($class, $alias);
        $finder->createQueryBuilder($repo->reveal(), ['foo' => 'val']);
    }
}
