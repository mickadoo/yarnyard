<?php

namespace YarnyardBundle\Test\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\SentenceRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Service\StoryCompletionService;

class StoryCompletionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldNotCompleteIfNoLimit()
    {
        $story = new Story();
        $story->setNumSentences(0);

        $repo = $this->prophesize(SentenceRepository::class);
        $manager = $this->prophesize(EntityManager::class);

        $service = new StoryCompletionService(
            $repo->reveal(),
            $manager->reveal()
        );

        $this->assertFalse($service->shouldComplete($story));
    }

    /**
     * @test
     */
    public function shouldNotCompleteIfHasNotReachedLimit()
    {
        $story = new Story();
        $story->setNumSentences(10);

        $repo = $this->prophesize(SentenceRepository::class);
        $manager = $this->prophesize(EntityManager::class);
        $repo->getCount($story)->willReturn(9);

        $service = new StoryCompletionService(
            $repo->reveal(),
            $manager->reveal()
        );

        $this->assertFalse($service->shouldComplete($story));
    }

    /**
     * @test
     */
    public function shouldCompleteIfHasReachedLimit()
    {
        $story = new Story();
        $story->setNumSentences(10);

        $repo = $this->prophesize(SentenceRepository::class);
        $manager = $this->prophesize(EntityManager::class);
        $repo->getCount($story)->willReturn(10);

        $service = new StoryCompletionService(
            $repo->reveal(),
            $manager->reveal()
        );

        $this->assertTrue($service->shouldComplete($story));
    }
}
