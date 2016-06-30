<?php

namespace YarnyardBundle\Test\Service;

use Prophecy\Argument;
use YarnyardBundle\Entity\ParticipationGrantRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Exception\NoAvailableUserException;
use YarnyardBundle\Service\ParticipantLimitCalculator;
use YarnyardBundle\Service\ParticipationGrantCreator;
use YarnyardBundle\Service\RandomParticipantAdder;
use YarnyardBundle\Service\RandomParticipantFinder;

class RandomParticipantAdderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldNotAddForNonRandom()
    {
        $story = new Story();
        $story->setRandom(false);

        $adder = $this->getAdder();

        $this->assertFalse($adder->shouldAddParticipant($story));
    }

    /**
     * @param null $repo
     * @param null $calculator
     * @param null $finder
     * @param null $creator
     *
     * @return RandomParticipantAdder
     */
    private function getAdder($repo = null, $calculator = null, $finder = null, $creator = null)
    {
        if (!$repo) {
            $repo = $this->prophesize(ParticipationGrantRepository::class);
        }
        if (!$calculator) {
            $calculator = $this->prophesize(ParticipantLimitCalculator::class);
        }
        if (!$finder) {
            $finder = $this->prophesize(RandomParticipantFinder::class);
        }
        if (!$creator) {
            $creator = $this->prophesize(ParticipationGrantCreator::class);
        }

        return new RandomParticipantAdder(
            $calculator->reveal(),
            $repo->reveal(),
            $finder->reveal(),
            $creator->reveal()
        );
    }

    /**
     * @test
     */
    public function shouldNotAddIfReachedLimit()
    {
        $story = new Story();
        $story->setRandom(true);

        $repo = $this->prophesize(ParticipationGrantRepository::class);
        $repo->getCount($story)->willReturn(10)->shouldBeCalled();
        $calculator = $this->prophesize(ParticipantLimitCalculator::class);
        $calculator->getLimit($story)->willReturn(10);

        $adder = $this->getAdder($repo, $calculator);

        $this->assertFalse($adder->shouldAddParticipant($story));
    }

    /**
     * @test
     */
    public function shouldAddIfNotReachedLimit()
    {
        $story = new Story();
        $story->setRandom(true);

        $repo = $this->prophesize(ParticipationGrantRepository::class);
        $repo->getCount($story)->willReturn(10)->shouldBeCalled();
        $calculator = $this->prophesize(ParticipantLimitCalculator::class);
        $calculator->getLimit($story)->willReturn(11);

        $adder = $this->getAdder($repo, $calculator);

        $this->assertTrue($adder->shouldAddParticipant($story));
    }

    /**
     * @test
     */
    public function willNotAddIfNoAvailableParticipants()
    {
        $story = new Story();

        $finder = $this->prophesize(RandomParticipantFinder::class);
        $finder->find($story)->willThrow(new NoAvailableUserException(''));
        $creator = $this->prophesize(ParticipationGrantCreator::class);
        $creator->create(Argument::any(), Argument::any())->shouldNotBeCalled();

        $adder = $this->getAdder(null, null, $finder, $creator);

        $adder->addRandomParticipant($story);
    }

    /**
     * @test
     */
    public function willAddIfAvailableParticipant()
    {
        $story = new Story();
        $user = new User('foo');

        $finder = $this->prophesize(RandomParticipantFinder::class);
        $finder->find($story)->willReturn($user);
        $creator = $this->prophesize(ParticipationGrantCreator::class);
        $creator->create($story, $user)->shouldBeCalled();

        $adder = $this->getAdder(null, null, $finder, $creator);

        $adder->addRandomParticipant($story);
    }
}
