<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\Sentence;
use YarnyardBundle\Entity\Story;

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
        // todo validate story is not complete, current user is allowed to add

        $sentence = new Sentence();
        $sentence
            ->setText($text)
            ->setStory($story);

        $this->manager->persist($sentence);
        $this->manager->flush($sentence);

        return $sentence;
    }
}
