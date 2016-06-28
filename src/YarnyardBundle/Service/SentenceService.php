<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use YarnyardBundle\Entity\Sentence;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Event\Sentence\SentenceCreatedEvent;
use YarnyardBundle\Exception\ValidationException;

class SentenceService
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var TurnTracker
     */
    protected $turnTracker;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EntityManager            $manager
     * @param TokenStorageInterface    $tokenStorage
     * @param TurnTracker              $turnTracker
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EntityManager $manager,
        TokenStorageInterface $tokenStorage,
        TurnTracker $turnTracker,
        EventDispatcherInterface $dispatcher
    ) {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->turnTracker = $turnTracker;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Story  $story
     * @param string $text
     *
     * @return Sentence
     */
    public function create(Story $story, string $text) : Sentence
    {
        if ($story->isCompleted()) {
            throw new ValidationException('Story is already complete');
        }

        $currentUser = $this->tokenStorage->getToken()->getUser();

        if ($currentUser !== $this->turnTracker->getCurrentTurnUser($story)) {
            throw new ValidationException('It\'s not your turn');
        }

        $sentence = new Sentence();
        $sentence
            ->setText($text)
            ->setStory($story);

        $this->manager->persist($sentence);
        $this->manager->flush($sentence);

        $event = new SentenceCreatedEvent($sentence);
        $this->dispatcher->dispatch($event::NAME, $event);

        return $sentence;
    }
}
