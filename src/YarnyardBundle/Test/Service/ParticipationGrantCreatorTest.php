<?php

namespace YarnyardBundle\Test\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Entity\ParticipationGrantRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Exception\YarnyardException;
use YarnyardBundle\Service\ParticipationGrantCreator;

class ParticipationGrantCreatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function willFailIfGrantExists()
    {
        $this->expectException(YarnyardException::class);
        $this->expectExceptionMessage('that grant already exists');

        $story = new Story();
        $user = new User('uuid');
        $grant = new ParticipationGrant();

        $manager = $this->prophesize(EntityManager::class);
        $repo = $this->prophesize(ParticipationGrantRepository::class);
        $repo->findBy(['story' => $story, 'user' => $user])->willReturn($grant);

        $creator = new ParticipationGrantCreator(
            $manager->reveal(),
            $repo->reveal()
        );

        $creator->create($story, $user);
    }

    /**
     * @test
     */
    public function willFailForCompletedStory()
    {
        $this->expectException(YarnyardException::class);
        $this->expectExceptionMessage('cannot change completed stories');

        $story = new Story();
        $story->setCompleted(true);
        $user = new User('uuid');

        $manager = $this->prophesize(EntityManager::class);
        $repo = $this->prophesize(ParticipationGrantRepository::class);

        $creator = new ParticipationGrantCreator(
            $manager->reveal(),
            $repo->reveal()
        );

        $creator->create($story, $user);
    }

    /**
     * @test
     */
    public function willWorkAsExpected()
    {
        $story = new Story();
        $user = new User('uuid');

        $manager = $this->prophesize(EntityManager::class);
        $repo = $this->prophesize(ParticipationGrantRepository::class);

        $creator = new ParticipationGrantCreator(
            $manager->reveal(),
            $repo->reveal()
        );

        $grant = $creator->create($story, $user);

        static::assertEquals($user, $grant->getUser());
        static::assertEquals($story, $grant->getStory());
    }
}
