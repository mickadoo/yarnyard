<?php

namespace Tests\YarnyardBundle\EventListener\Sentence;

use Prophecy\Argument;
use YarnyardBundle\Entity\Sentence;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Event\Sentence\SentenceCreatedEvent;
use YarnyardBundle\EventListener\Sentence\SentenceCreatedEventListener;
use YarnyardBundle\Service\RandomParticipantAdder;
use YarnyardBundle\Service\StoryCompletionService;
use YarnyardBundle\Service\TurnTracker;
use YarnyardBundle\Util\RabbitMQ\Connection;

class SentenceCreatedEventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function ifShouldCompleteThenDoesNothingElse()
    {
        $story = new Story();
        $sentence = new Sentence();
        $sentence->setStory($story);
        $event = new SentenceCreatedEvent($sentence);

        $completionService = $this->prophesize(StoryCompletionService::class);
        $completionService->shouldComplete($story)->willReturn(true);
        $completionService->complete($story)->shouldBeCalled();

        $adder = $this->prophesize(RandomParticipantAdder::class);
        $adder->shouldAddParticipant(Argument::any())->shouldNotBeCalled();

        $listener = $this->getListener($completionService);
        $listener->onSentenceCreated($event);
    }

    /**
     * @param null $completionService
     * @param null $adder
     * @param null $rabbit
     * @param null $turnTracker
     *
     * @return SentenceCreatedEventListener
     */
    private function getListener(
        $completionService = null,
        $adder = null,
        $turnTracker = null,
        $rabbit = null
    ) {
        if (!$completionService) {
            $completionService = $this->prophesize(StoryCompletionService::class);
        }
        if (!$adder) {
            $adder = $this->prophesize(RandomParticipantAdder::class);
        }
        if (!$turnTracker) {
            $turnTracker = $this->prophesize(TurnTracker::class);
        }
        if (!$rabbit) {
            $rabbit = $this->prophesize(Connection::class);
        }

        return new SentenceCreatedEventListener(
            $completionService->reveal(),
            $adder->reveal(),
            $rabbit->reveal(),
            $turnTracker->reveal()
        );
    }

    /**
     * @test
     */
    public function willAddNewParticipantIfNeededAndNotifyThem()
    {
        $story = new Story();
        $sentence = new Sentence();
        $sentence->setStory($story);
        $event = new SentenceCreatedEvent($sentence);
        $sentenceAuthor = new User('foo');
        $nextUser = new User('bar');
        $sentence->setCreatedBy($sentenceAuthor);

        $completionService = $this->prophesize(StoryCompletionService::class);
        $completionService->shouldComplete($story)->willReturn(false);
        $completionService->complete(Argument::any())->shouldNotBeCalled();

        $adder = $this->prophesize(RandomParticipantAdder::class);
        $adder->shouldAddParticipant($story)->willReturn(true);
        $adder->addRandomParticipant($story)->shouldBeCalled();

        $turnTracker = $this->prophesize(TurnTracker::class);
        $turnTracker->getCurrentTurnUser($story)->willReturn($nextUser);

        $rabbit = $this->prophesize(Connection::class);
        // will contain uuid of next user in json string
        $rabbit->publish('yarnyard', Argument::containingString($nextUser->getUuid()))->shouldBeCalled();

        $listener = $this->getListener($completionService, $adder, $turnTracker, $rabbit);
        $listener->onSentenceCreated($event);
    }

    /**
     * @test
     */
    public function willNotNotifyParticipantIfSameAsLast()
    {
        $story = new Story();
        $sentence = new Sentence();
        $sentence->setStory($story);
        $event = new SentenceCreatedEvent($sentence);
        $sentenceAuthor = new User('foo');
        $sentence->setCreatedBy($sentenceAuthor);

        $completionService = $this->prophesize(StoryCompletionService::class);
        $completionService->shouldComplete($story)->willReturn(false);
        $completionService->complete(Argument::any())->shouldNotBeCalled();

        $adder = $this->prophesize(RandomParticipantAdder::class);
        $adder->shouldAddParticipant($story)->willReturn(false);
        $adder->addRandomParticipant($story)->shouldNotBeCalled();

        $turnTracker = $this->prophesize(TurnTracker::class);
        $turnTracker->getCurrentTurnUser($story)->willReturn($sentenceAuthor);

        $rabbit = $this->prophesize(Connection::class);
        // will contain uuid of next user in json string
        $rabbit->publish(Argument::any(), Argument::any())->shouldNotBeCalled();

        $listener = $this->getListener($completionService, $adder, $turnTracker, $rabbit);
        $listener->onSentenceCreated($event);
    }
}
