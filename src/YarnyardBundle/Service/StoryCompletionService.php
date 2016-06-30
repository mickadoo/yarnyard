<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\SentenceRepository;
use YarnyardBundle\Entity\Story;

class StoryCompletionService
{
    /**
     * @var SentenceRepository
     */
    protected $sentenceRepo;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param SentenceRepository $sentenceRepo
     * @param EntityManager      $manager
     */
    public function __construct(
        SentenceRepository $sentenceRepo,
        EntityManager $manager
    ) {
        $this->sentenceRepo = $sentenceRepo;
        $this->manager = $manager;
    }

    /**
     * A story should be automatically completed if it has reached its
     * sentence limit.
     *
     * @param Story $story
     *
     * @return bool
     */
    public function shouldComplete(Story $story) : bool
    {
        if (!$story->hasSentenceLimit()) {
            return false;
        }

        $sentenceCount = $this->sentenceRepo->getCount($story);

        return $sentenceCount >= $story->getNumSentences();
    }

    /**
     * @param Story $story
     */
    public function complete(Story $story)
    {
        $story->setCompleted(true);
        $this->manager->persist($story);
        $this->manager->flush($story);
    }
}
