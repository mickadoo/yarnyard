<?php

namespace Mickadoo\SearchBundle\Service;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mickadoo\SearchBundle\Util\DQLNode;
use Mickadoo\SearchBundle\Util\PropertyParser;

/**
 * Create query builder to find entities based on array of values.
 * Class metadata is used to decide which parameters are allowed.
 * Check the README for information on parameter format.
 */
class EntityFinder
{
    const OPERATOR_OR = 'OR';
    const OPERATOR_AND = 'AND';

    /**
     * @var string
     */
    protected $strategy = self::OPERATOR_OR;

    /**
     * @var PropertyParser
     */
    protected $parser;

    /**
     * @var MappingFetcher
     */
    protected $fetcher;

    /**
     * @var EntityValueValidator
     */
    protected $validator;

    /**
     * @var DQLValueFormatter
     */
    protected $formatter;

    /**
     * @param PropertyParser       $parser
     * @param MappingFetcher       $fetcher
     * @param EntityValueValidator $validator
     * @param DQLValueFormatter    $formatter
     */
    public function __construct(
        PropertyParser $parser,
        MappingFetcher $fetcher,
        EntityValueValidator $validator,
        DQLValueFormatter $formatter
    ) {
        $this->parser = $parser;
        $this->fetcher = $fetcher;
        $this->validator = $validator;
        $this->formatter = $formatter;
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
        $alias = $this->getClassAlias($class);
        $queryBuilder = $repo->createQueryBuilder($alias);

        foreach ($params as $field => $valueString) {
            // if argument is related entity remove 'Id' suffix
            if (substr($field, -2) === 'Id') {
                $field = substr($field, 0, -2);
            }

            if (!$this->hasProperty($class, $field)) {
                continue;
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

            $whereDql = $this->createDQL($whereParts, $class, $field);

            $queryBuilder->andWhere($whereDql);
        }

        return $queryBuilder;
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private function getClassAlias(string $class) : string
    {
        return strtolower(substr($class, strrpos($class, '\\') + 1));
    }

    /**
     * @param string $className
     * @param string $property
     *
     * @return bool
     */
    private function hasProperty(string $className, string $property) : bool
    {
        return in_array($property, $this->fetcher->getFields($className));
    }

    /**
     * @param DQLNode[] $nodes
     * @param string    $class
     * @param string    $field
     *
     * @return string
     *
     * @throws \Exception
     */
    private function createDQL(array $nodes, string $class, string $field) : string
    {
        $column = sprintf('%s.%s', $this->getClassAlias($class), $field);
        $whereDql = '';

        foreach ($nodes as $node) {
            $whereDql .= sprintf(
                ' %s %s %s %s',
                $this->strategy,
                $column,
                $node->getOperator(),
                $this->formatter->format($class, $field, $node->getValue())
            );
        }

        // remove superfluous OR
        return substr($whereDql, 3);
    }
}
