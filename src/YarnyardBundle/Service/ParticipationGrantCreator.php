<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;

class ParticipationGrantCreator
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Story $story
     * @param User  $user
     *
     * @return ParticipationGrant
     */
    public function create(Story $story, User $user) : ParticipationGrant
    {
        $grant = new ParticipationGrant();
        $grant
            ->setUser($user)
            ->setStory($story);

        $this->manager->persist($grant);
        $this->manager->flush($grant);

        return $grant;
    }
}
