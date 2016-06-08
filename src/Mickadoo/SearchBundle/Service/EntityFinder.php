<?php

namespace Mickadoo\SearchBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use Mickadoo\SearchBundle\Util\DQLNode;
use Mickadoo\SearchBundle\Util\PropertyParser;

/**
 * Find entities based on array of values
 * Class metadata is used to decide which parameters are allowed
 * Check the README for information on parameter format.
 */
class EntityFinder
{
    const OPERATOR_OR = 'OR';
    const OPERATOR_AND = 'AND';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var PropertyParser
     */
    protected $parser;

    /**
     * @param EntityManager  $entityManager
     * @param PropertyParser $parser
     */
    public function __construct(EntityManager $entityManager, PropertyParser $parser)
    {
        $this->entityManager = $entityManager;
        $this->parser = $parser;
    }

    /**
     * @param string $entityClass
     * @param array  $parameters
     * @param string $operator
     *
     * @return array
     */
    public function find(string $entityClass, array $parameters, string $operator = self::OPERATOR_OR)
    {
        $repository = $this->entityManager->getRepository($entityClass);

        return $this->createQueryBuilder($repository, $parameters, $operator)->getQuery()->getResult();
    }

    /**
     * @param EntityRepository $repository
     * @param array            $parameters
     * @param string           $operator
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(
        EntityRepository $repository,
        array $parameters,
        string $operator = self::OPERATOR_OR
    ) {
        $className = $repository->getClassName();
        $alias = strtolower(substr($className, strrpos($className, '\\') + 1));
        $queryBuilder = $repository->createQueryBuilder($alias);

        // loop through parameters
        foreach ($parameters as $property => $valueString) {
            // if argument is related entity remove 'Id' suffix
            if (substr($property, -2) === 'Id') {
                $property = substr($property, 0, -2);
            }

            // check if parameter matches property on entity
            if (!$this->entityHasProperty($className, $property)) {
                continue;
            }

            // todo maybe this could be moved to a service specializing in creating DQL or validating it
            $column = $alias.'.'.$property;
            $whereParts = $this->parser->parse($valueString);

            // validation
            $whereParts = array_filter($whereParts, function (DQLNode $node) use ($className, $property) {
                return $this->isValueAllowed($className, $property, $node->getValue());
            });

            // DQL creation
            $whereDql = array_reduce($whereParts, function ($carry, DQLNode $node) use ($column, $operator) {
                return sprintf(
                    '%s %s %s %s %s',
                    $carry,
                    $operator,
                    $column,
                    $node->getOperator(),
                    $node->getValue()
                );
            });
            // remove superfluous OR
            $whereDql = substr($whereDql, 3);

            $queryBuilder->andWhere($whereDql);
        }

        return $queryBuilder;
    }

    /**
     * @param string $className
     * @param string $property
     *
     * @return bool
     */
    private function entityHasProperty(string $className, string $property) : bool
    {
        return in_array($property, $this->entityManager->getClassMetadata($className)->getFieldNames());
    }

    /**
     * @param string $className
     * @param string $property
     * @param mixed  $value
     *
     * @return bool
     *
     * @throws MappingException
     */
    private function isValueAllowed(string $className, string $property, mixed $value) : bool
    {
        $mapping = $this->entityManager->getClassMetadata($className)->getFieldMapping($property);

        switch ($mapping['type']) {
            case 'integer':
                return filter_var($value, FILTER_VALIDATE_INT);
            default:
                return false;
        }
    }
}
