<?php

namespace YarnyardBundle\Service;

use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Entity\ParticipationGrantRepository;
use YarnyardBundle\Entity\Story;

/**
 * The goal of the sorter is to provide sorted participants for a story
 * Participants are ordered by the date they were added.
 */
class ParticipantSorter
{
    /**
     * @var ParticipationGrantRepository
     */
    protected $repo;

    /**
     * @param ParticipationGrantRepository $repo
     */
    public function __construct(ParticipationGrantRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Story $story
     *
     * @return ParticipationGrant[]
     */
    public function getSortedParticipants(Story $story) : array
    {
        $grants = $this->repo->findBy(['story' => $story]);

        $userMapper = function (ParticipationGrant $grant) {
            return $grant->getUser();
        };

        $sortedGrants = $this->sortByAddedDate($grants);

        return array_values(array_map($userMapper, $sortedGrants));
    }

    /**
     * @param ParticipationGrant[] $grants
     *
     * @return ParticipationGrant[]
     */
    private function sortByAddedDate(array $grants) : array
    {
        $comparator = function (
            ParticipationGrant $grantA,
            ParticipationGrant $grantB
        ) {
            return $grantA->getCreatedAt() <=> $grantB->getCreatedAt();
        };

        uasort($grants, $comparator);

        return $grants;
    }
}
