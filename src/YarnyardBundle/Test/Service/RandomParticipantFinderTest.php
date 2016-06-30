<?php

namespace YarnyardBundle\Test\Service;

use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Entity\ParticipationGrantRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Entity\UserRepository;
use YarnyardBundle\Service\RandomParticipantFinder;

class RandomParticipantFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function findWillWork()
    {
        $userRepo = $this->prophesize(UserRepository::class);
        $participantRepo = $this->prophesize(ParticipationGrantRepository::class);
        $story = new Story();
        $userOne = $this->prophesize(User::class);
        $userOne->getId()->willReturn(1);
        $userTwo = $this->prophesize(User::class);
        $userTwo->getId()->willReturn(2);
        $userThree = $this->prophesize(User::class);
        $userThree->getId()->willReturn(3);
        $grantOne = new ParticipationGrant();
        $grantOne->setUser($userOne->reveal());
        $grantTwo = new ParticipationGrant();
        $grantTwo->setUser($userTwo->reveal());
        $grants = [$grantOne, $grantTwo];

        $participantRepo->findBy(['story' => $story])->willReturn($grants);
        $userRepo->findRandom([1, 2])->willReturn($userThree->reveal());

        $finder = new RandomParticipantFinder(
            $userRepo->reveal(),
            $participantRepo->reveal()
        );

        $result = $finder->find($story);

        $this->assertSame(3, $result->getId());
    }
}
