<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\Story;

class StoryService
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
     * @param string $title
     * @param bool   $random
     * @param int    $rounds
     *
     * @return Story
     */
    public function create(string $title, bool $random, int $rounds) : Story
    {
        $story = new Story();
        $story
            ->setTitle($title)
            ->setRandom($random)
            ->setNumSentences($rounds);

        $this->manager->persist($story);
        $this->manager->flush($story);

        return $story;
    }
}
