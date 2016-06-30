<?php

namespace YarnyardBundle\EventListener\Sentence;

use YarnyardBundle\Event\Sentence\SentenceCreatedEvent;
use YarnyardBundle\Service\RandomParticipantAdder;
use YarnyardBundle\Service\StoryCompletionService;

class SentenceCreatedEventListener
{
    /**
     * @var StoryCompletionService
     */
    protected $completionService;

    /**
     * @var RandomParticipantAdder
     */
    protected $participantAdder;

    /**
     * @param StoryCompletionService $completionService
     * @param RandomParticipantAdder $participantAdder
     */
    public function __construct(
        StoryCompletionService $completionService,
        RandomParticipantAdder $participantAdder
    ) {
        $this->completionService = $completionService;
        $this->participantAdder = $participantAdder;
    }

    /**
     * @param SentenceCreatedEvent $event
     */
    public function onSentenceCreated(SentenceCreatedEvent $event)
    {
        $sentence = $event->getSentence();
        $story = $sentence->getStory();

        if ($this->completionService->shouldComplete($story)) {
            $this->completionService->complete($story);

            return;
        }

        // if random and not completed add new participant
        if ($this->participantAdder->shouldAddParticipant($story)) {
            $this->participantAdder->addRandomParticipant($story);
        }

        // todo notify next participant
    }
}
