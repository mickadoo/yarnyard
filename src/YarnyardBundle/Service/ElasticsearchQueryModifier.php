<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Elastica\Index;
use Elastica\Result;
use YarnyardBundle\Exception\YarnyardException;

class ElasticsearchQueryModifier
{
    /**
     * @var Index
     */
    protected $index;

    /**
     * @param Index $index
     */
    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    /**
     * @param $searchTerm
     * @param QueryBuilder $queryBuilder
     *
     * @throws YarnyardException
     */
    public function addModifierToQuery($searchTerm, QueryBuilder $queryBuilder)
    {
        if (count($queryBuilder->getRootAliases()) > 1) {
            throw new YarnyardException('Your query isn\'t suitable, it\'s got more than one select alias');
        }

        $alias = $queryBuilder->getRootAliases()[0];

        if (!array_key_exists($alias, $this->index->getMapping())) {
            throw new YarnyardException('You gotta use an alias that corresponds to the elasticsearch mapping');
        }

        $type = $this->index->getType($alias);

        $hits = array_map(
            function (Result $result) {
                return $result->getId();
            },
            $type->search($searchTerm)->getResults()
        );

        $queryBuilder->where($queryBuilder->expr()->in($alias . '.id', ':hits'))->setParameter('hits', $hits);
    }
}
