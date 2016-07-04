<?php

namespace YarnyardBundle\EventListener\Sentence;

use YarnyardBundle\Entity\User;
use YarnyardBundle\Event\Sentence\SentenceCreatedEvent;
use YarnyardBundle\Service\RandomParticipantAdder;
use YarnyardBundle\Service\StoryCompletionService;
use YarnyardBundle\Service\TurnTracker;
use YarnyardBundle\Util\RabbitMQ\Connection;

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
     * @var Connection
     */
    protected $rabbit;

    /**
     * @var TurnTracker
     */
    protected $turnTracker;

    /**
     * @param StoryCompletionService $completionService
     * @param RandomParticipantAdder $participantAdder
     * @param Connection             $rabbit
     * @param TurnTracker            $turnTracker
     */
    public function __construct(
        StoryCompletionService $completionService,
        RandomParticipantAdder $participantAdder,
        Connection $rabbit,
        TurnTracker $turnTracker
    ) {
        $this->completionService = $completionService;
        $this->participantAdder = $participantAdder;
        $this->rabbit = $rabbit;
        $this->turnTracker = $turnTracker;
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

        // get current turn user
        $currentTurnUser = $this->turnTracker->getCurrentTurnUser($story);

        // in stories with only one user we don't want to notify them again
        if ($sentence->getCreatedBy() === $currentTurnUser) {
            return;
        }

        $this->notifyNextParticipant($currentTurnUser);
    }

    /**
     * @param User $currentTurnUser
     */
    private function notifyNextParticipant(User $currentTurnUser)
    {
        $body = [
            'type' => 'its_your_turn',
            'data' => [
                'user' => [
                    'uuid' => $currentTurnUser->getUuid(),
                ],
            ],
        ];

        // notify next participant
        $this->rabbit->publish('yarnyard', json_encode($body));
    }
}
