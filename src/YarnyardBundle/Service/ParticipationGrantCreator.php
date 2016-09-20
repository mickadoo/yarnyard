<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Entity\ParticipationGrantRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Exception\YarnyardException;

class ParticipationGrantCreator
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var ParticipationGrantRepository
     */
    protected $participantGrantRepo;

    /**
     * @param EntityManager                $manager
     * @param ParticipationGrantRepository $participantGrantRepo
     */
    public function __construct(
        EntityManager $manager,
        ParticipationGrantRepository $participantGrantRepo
    ) {
        $this->manager = $manager;
        $this->participantGrantRepo = $participantGrantRepo;
    }

    /**
     * @param Story $story
     * @param User  $user
     *
     * @return ParticipationGrant
     *
     * @throws YarnyardException
     */
    public function create(Story $story, User $user) : ParticipationGrant
    {
        // if user already has grant for story
        $existing = $this->participantGrantRepo->findBy(
            ['story' => $story, 'user' => $user]
        );

        if ($existing) {
            throw new YarnyardException('that grant already exists');
        }

        if ($story->isCompleted()) {
            throw new YarnyardException('cannot change completed stories');
        }

        $grant = new ParticipationGrant();
        $grant
            ->setUser($user)
            ->setStory($story);

        $this->manager->persist($grant);
        $this->manager->flush($grant);

        return $grant;
    }
}
