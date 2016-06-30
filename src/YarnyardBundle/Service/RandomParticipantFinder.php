<?php

namespace YarnyardBundle\Service;

use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Entity\ParticipationGrantRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Entity\UserRepository;

class RandomParticipantFinder
{
    /**
     * @var UserRepository
     */
    protected $userRepo;

    /**
     * @var ParticipationGrantRepository
     */
    protected $participantRepo;

    /**
     * @param UserRepository               $userRepo
     * @param ParticipationGrantRepository $participantRepo
     */
    public function __construct(
        UserRepository $userRepo,
        ParticipationGrantRepository $participantRepo
    ) {
        $this->userRepo = $userRepo;
        $this->participantRepo = $participantRepo;
    }

    /**
     * @param Story $story
     *
     * @return User
     */
    public function find(Story $story) : User
    {
        $grants = $this->participantRepo->findBy(['story' => $story]);
        $mapper = function (ParticipationGrant $grant) {
            return $grant->getUser()->getId();
        };
        $participantIds = array_map($mapper, $grants);

        return $this->userRepo->findRandom($participantIds);
    }
}
