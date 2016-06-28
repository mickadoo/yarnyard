<?php

namespace YarnyardBundle\Event\Sentence;

use Symfony\Component\EventDispatcher\Event;
use YarnyardBundle\Entity\Sentence;

class SentenceCreatedEvent extends Event
{
    const NAME = 'sentence.created';

    /**
     * @var Sentence
     */
    protected $sentence;

    /**
     * @param Sentence $sentence
     */
    public function __construct(Sentence $sentence)
    {
        $this->sentence = $sentence;
    }

    /**
     * @return Sentence
     */
    public function getSentence()
    {
        return $this->sentence;
    }
}
