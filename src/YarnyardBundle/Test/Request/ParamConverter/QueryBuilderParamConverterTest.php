<?php

namespace YarnyardBundle\Test\Request\ParamConverter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Mickadoo\SearchBundle\Service\EntityFinder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use YarnyardBundle\Request\ParamConverter\QueryBuilderParamConverter;
use YarnyardBundle\Service\ElasticsearchQueryModifier;

class QueryBuilderParamConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider supportsDataProvider
     *
     * @param array  $config
     * @param string $class
     * @param bool   $expected
     */
    public function supportsWillWork(array $config, $class, $expected)
    {
        $finder = $this->prophesize(EntityFinder::class);
        $searchModifier = $this->prophesize(ElasticsearchQueryModifier::class);
        $manager = $this->prophesize(EntityManager::class);
        $config = new ParamConverter(['class' => $class, 'options' => $config]);

        $converter = new QueryBuilderParamConverter(
            $finder->reveal(),
            $searchModifier->reveal(),
            $manager->reveal()
        );

        $this->assertEquals($expected, $converter->supports($config));
    }

    /**
     * @return array
     */
    public function supportsDataProvider()
    {
        return [
            [
                [],
                'IAMABADCLASS',
                false,
            ],
            [
                [],
                QueryBuilder::class,
                false,
            ],
            [
                ['class' => \stdClass::class],
                'IAMABADCLASS',
                false,
            ],
            [
                ['class' => \stdClass::class],
                QueryBuilder::class,
                true,
            ],
        ];
    }
}
