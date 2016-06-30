<?php

namespace YarnyardBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ParticipationGrantRepository extends EntityRepository
{
    /**
     * @param Story $story
     *
     * @return int
     */
    public function getCount(Story $story) : int
    {
        $query = $this
            ->createQueryBuilder('participationGrant')
            ->select('COUNT(participationGrant.id)')
            ->where('participationGrant.story = :story')
            ->setParameter('story', $story);

        return $query->getQuery()->getSingleScalarResult();
    }
}
