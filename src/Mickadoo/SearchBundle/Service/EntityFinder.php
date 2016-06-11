<?php

namespace Mickadoo\SearchBundle\Service;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mickadoo\SearchBundle\Util\AliasGenerator;
use Mickadoo\SearchBundle\Util\DQLNode;
use Mickadoo\SearchBundle\Util\PropertyParser;

/**
 * Create query builder to find entities based on array of values.
 * Class metadata is used to decide which parameters are allowed.
 * Check the README for information on parameter format.
 */
class EntityFinder
{
    /**
     * @var PropertyParser
     */
    protected $parser;

    /**
     * @var EntityValueValidator
     */
    protected $validator;

    /**
     * @var DQLPartCreator
     */
    protected $partCreator;

    /**
     * @var AliasGenerator
     */
    protected $aliasGenerator;

    /**
     * @param PropertyParser       $parser
     * @param EntityValueValidator $validator
     * @param DQLPartCreator       $partCreator
     * @param AliasGenerator       $aliasGenerator
     */
    public function __construct(
        PropertyParser $parser,
        EntityValueValidator $validator,
        DQLPartCreator $partCreator,
        AliasGenerator $aliasGenerator
    ) {
        $this->parser = $parser;
        $this->validator = $validator;
        $this->partCreator = $partCreator;
        $this->aliasGenerator = $aliasGenerator;
    }

    /**
     * @param EntityRepository $repo
     * @param array            $params
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(EntityRepository $repo, array $params) : QueryBuilder
    {
        $class = $repo->getClassName();
        $alias = $this->aliasGenerator->generate($class);
        $queryBuilder = $repo->createQueryBuilder($alias);

        foreach ($params as $field => $valueString) {
            // if argument is related entity remove 'Id' suffix
            if (substr($field, -2) === 'Id') {
                $field = substr($field, 0, -2);
            }

            $whereParts = $this->parser->parse($valueString);

            $validationFilter = function (DQLNode $node) use ($class, $field) {
                return $this->validator->isValid(
                    $class,
                    $field,
                    $node->getValue()
                );
            };

            // remove invalid parts
            $whereParts = array_filter($whereParts, $validationFilter);

            if (empty($whereParts)) {
                continue;
            }

            $whereDql = $this->partCreator->create($whereParts, $class, $field);

            $queryBuilder->andWhere($whereDql);
        }

        return $queryBuilder;
    }
}
