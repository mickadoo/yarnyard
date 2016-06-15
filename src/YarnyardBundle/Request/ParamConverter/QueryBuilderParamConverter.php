<?php

namespace YarnyardBundle\Request\ParamConverter;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Mickadoo\SearchBundle\Service\EntityFinder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use YarnyardBundle\Exception\YarnyardException;
use YarnyardBundle\Service\ElasticsearchQueryModifier;

class QueryBuilderParamConverter implements ParamConverterInterface
{
    /**
     * @var EntityFinder
     */
    protected $finder;

    /**
     * @var ElasticsearchQueryModifier
     */
    protected $searchModifier;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param EntityFinder               $finder
     * @param ElasticsearchQueryModifier $searchModifier
     * @param EntityManager              $manager
     */
    public function __construct(
        EntityFinder $finder,
        ElasticsearchQueryModifier $searchModifier,
        EntityManager $manager
    ) {
        $this->finder = $finder;
        $this->searchModifier = $searchModifier;
        $this->manager = $manager;
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool
     *
     * @throws YarnyardException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getOptions()['class'];

        try {
            $repo = $this->manager->getRepository($class);
        } catch (MappingException $exception) {
            throw new YarnyardException("$class isn't managed by doctrine");
        }

        $params = $request->query->all();
        $search = $request->query->get('search');
        $queryBuilder = $this->finder->createQueryBuilder($repo, $params);

        if ($search) {
            $this->searchModifier->addModifierToQuery($search, $queryBuilder);
        }

        $request->attributes->set($configuration->getName(), $queryBuilder);

        return true;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration) : bool
    {
        $class = $configuration->getOptions()['class'] ?? null;
        $supportsClass = $configuration->getClass() === QueryBuilder::class;

        return $supportsClass && class_exists($class);
    }
}
