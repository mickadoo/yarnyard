<?php

namespace YarnyardBundle\Test\Service;

use Prophecy\Argument;
use YarnyardBundle\Entity\Sentence;
use YarnyardBundle\Entity\SentenceRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Exception\YarnyardException;
use YarnyardBundle\Service\ParticipantSorter;
use YarnyardBundle\Service\TurnTracker;
use YarnyardBundle\Util\DateInterval\IntervalCounter;

class TurnTrackerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function storyWithoutParticipantsWillThrowException()
    {
        $story = new Story();
        $sorter = $this->prophesize(ParticipantSorter::class);
        $sorter->getSortedParticipants($story)->willReturn([]);
        $fetcher = $this->getFetcher($sorter);

        $this->expectException(YarnyardException::class);
        $fetcher->getCurrentTurnUser($story);
    }

    /**
     * @param null $sorter
     * @param null $repo
     *
     * @return TurnTracker
     */
    private function getFetcher($sorter = null, $repo = null)
    {
        if (!$sorter) {
            $sorter = $this->prophesize(ParticipantSorter::class);
        }
        if (!$repo) {
            $repo = $this->prophesize(SentenceRepository::class);
        }

        return new TurnTracker(
            $sorter->reveal(),
            $repo->reveal(),
            new IntervalCounter()
        );
    }

    /**
     * @test
     */
    public function newStoryWillReturnSecondParticipant()
    {
        $userA = new User('aaa');
        $userB = new User('bbb');
        $participants = [$userA, $userB];

        $story = $this->prophesize(Story::class);
        $story->getCreatedAt()->willReturn(new \DateTime('-10 minutes'));
        $story->getSkipTurnDuration()->willReturn(new \DateInterval('P5D'));
        $sorter = $this->prophesize(ParticipantSorter::class);
        $sorter->getSortedParticipants($story)->willReturn($participants);
        $repo = $this->prophesize(SentenceRepository::class);
        $repo->hasSentences($story)->willReturn(false);
        $repo->getMostRecent(Argument::any())->shouldNotBeCalled();
        $fetcher = $this->getFetcher($sorter, $repo);

        $result = $fetcher->getCurrentTurnUser($story->reveal());

        $this->assertEquals($userB, $result);
    }

    /**
     * @test
     */
    public function skippedStoryWillReturnFirstParticipant()
    {
        $userA = new User('aaa');
        $userB = new User('bbb');
        $userC = new User('ccc');
        $participants = [$userA, $userB, $userC];

        $story = $this->prophesize(Story::class);
        $story->getCreatedAt()->willReturn(new \DateTime('-3 days'));
        $story->getSkipTurnDuration()->willReturn(new \DateInterval('P1D'));
        $sorter = $this->prophesize(ParticipantSorter::class);
        $sorter->getSortedParticipants($story)->willReturn($participants);
        $repo = $this->prophesize(SentenceRepository::class);
        $repo->hasSentences($story)->willReturn(false);
        $repo->getMostRecent(Argument::any())->shouldNotBeCalled();
        $fetcher = $this->getFetcher($sorter, $repo);

        $result = $fetcher->getCurrentTurnUser($story->reveal());

        $this->assertEquals($userA, $result);
    }

    /**
     * @test
     */
    public function ifHasMostRecentThenNextUserWillBeChosen()
    {
        $userA = new User('aaa');
        $userB = new User('bbb');
        $userC = new User('ccc');
        $participants = [$userA, $userB, $userC];

        $story = $this->prophesize(Story::class);
        $story->getCreatedAt()->willReturn(new \DateTime('-3 days'));
        $story->getSkipTurnDuration()->willReturn(new \DateInterval('P1D'));

        $sentence = $this->prophesize(Sentence::class);
        $sentence->getCreatedBy()->willReturn($userB);
        $sentence->getCreatedAt()->willReturn(new \DateTime('today'));

        $sorter = $this->prophesize(ParticipantSorter::class);
        $sorter->getSortedParticipants($story)->willReturn($participants);

        $repo = $this->prophesize(SentenceRepository::class);
        $repo->hasSentences($story)->willReturn(true);
        $repo->getMostRecent($story)->willReturn($sentence->reveal());

        $fetcher = $this->getFetcher($sorter, $repo);

        $result = $fetcher->getCurrentTurnUser($story->reveal());

        $this->assertEquals($userC, $result);
    }
}
