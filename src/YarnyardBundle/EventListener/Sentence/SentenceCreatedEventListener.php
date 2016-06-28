<?php

namespace YarnyardBundle\EventListener\Sentence;

use YarnyardBundle\Event\Sentence\SentenceCreatedEvent;

class SentenceCreatedEventListener
{
    /**
     * @param SentenceCreatedEvent $event
     */
    public function onSentenceCreated(SentenceCreatedEvent $event)
    {
        $sentence = $event->getSentence();

        // check if story should be completed

        // if random and not completed add new participant

        // notify next participant
    }
}
