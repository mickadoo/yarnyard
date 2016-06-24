<?php

namespace YarnyardBundle\Test\Service;

use YarnyardBundle\Entity\ParticipationGrant;
use YarnyardBundle\Entity\ParticipationGrantRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Service\ParticipantSorter;

class NonRandomParticipantSorterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function sortWillWork()
    {
        $twoDaysAgo = $this->getMockGrant(5, '-2 days');
        $yesterday = $this->getMockGrant(1, 'yesterday');
        $todayNoon = $this->getMockGrant(2, 'today');
        $todayLate = $this->getMockGrant(2, 'today 22:00');
        $threeDaysAgo = $this->getMockGrant(3, '-3 days');

        $grants = [
            $todayNoon,
            $threeDaysAgo,
            $todayLate,
            $yesterday,
            $twoDaysAgo,
        ];

        $expectedOrder = [
            $threeDaysAgo->getUser()->getId(),
            $twoDaysAgo->getUser()->getId(),
            $yesterday->getUser()->getId(),
            $todayNoon->getUser()->getId(),
            $todayLate->getUser()->getId(),
        ];

        $story = new Story();

        $repo = $this->prophesize(ParticipationGrantRepository::class);
        $repo->findBy(['story' => $story])->willReturn($grants);

        $sorter = new ParticipantSorter($repo->reveal());
        $result = $sorter->getSortedParticipants($story);

        $ids = array_values(array_map(
            function (User $user) {
                return $user->getId();
            },
            $result
        ));

        $this->assertEquals($ids, $expectedOrder);
    }

    /**
     * @param $userId
     * @param $createdAtString
     *
     * @return ParticipationGrant
     */
    public function getMockGrant($userId, $createdAtString)
    {
        $user = $this->prophesize(User::class);
        $user->getId()->willReturn($userId);
        $grant = $this->prophesize(ParticipationGrant::class);
        $grant->getCreatedAt()->willReturn(new \DateTime($createdAtString));
        $grant->getUser()->willReturn($user->reveal());

        return $grant->reveal();
    }
}
