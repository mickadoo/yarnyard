<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\Sentence;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Exception\ValidationException;

class SentenceService
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
     * @param Story  $story
     * @param string $text
     *
     * @return Sentence
     */
    public function create(Story $story, string $text) : Sentence
    {
        // todo validate current user is allowed to add
        if ($story->isCompleted()) {
            throw new ValidationException('Story is already complete');
        }

        $sentence = new Sentence();
        $sentence
            ->setText($text)
            ->setStory($story);

        $this->manager->persist($sentence);
        $this->manager->flush($sentence);

        return $sentence;
    }
}
