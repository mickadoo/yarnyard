<?php

namespace YarnyardBundle\Service;

use YarnyardBundle\Entity\SentenceRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Exception\YarnyardException;
use YarnyardBundle\Util\DateInterval\IntervalCounter;

class TurnTracker
{
    /**
     * @var ParticipantSorter
     */
    protected $sorter;

    /**
     * @var SentenceRepository
     */
    protected $sentenceRepo;

    /**
     * @var IntervalCounter
     */
    protected $counter;

    /**
     * @param ParticipantSorter  $sorter
     * @param SentenceRepository $sentenceRepo
     * @param IntervalCounter    $counter
     */
    public function __construct(
        ParticipantSorter $sorter,
        SentenceRepository $sentenceRepo,
        IntervalCounter $counter
    ) {
        $this->sorter = $sorter;
        $this->sentenceRepo = $sentenceRepo;
        $this->counter = $counter;
    }

    /**
     * Gets the user whose turn it is currently based on most recent contributor
     * and number of skips since then.
     *
     * @param Story $story
     *
     * @return User
     */
    public function getCurrentTurnUser(Story $story) : User
    {
        $participants = $this->sorter->getSortedParticipants($story);

        if (empty($participants)) {
            throw new YarnyardException('Can\'t get next participant cos there are none');
        }

        // default last contributor is the first in order
        $lastContributorIndex = 0;
        // timer for skips starts at story creation
        $updatedAt = $story->getCreatedAt();

        // if contributions exist find who did the last one
        if ($this->sentenceRepo->hasSentences($story)) {
            $lastSentence = $this->sentenceRepo->getMostRecent($story);
            $updatedAt = $lastSentence->getCreatedAt();
            $lastContributorIndex = array_search(
                $lastSentence->getCreatedBy(),
                $participants,
                true
            );
        }

        $now = new \DateTime();
        $duration = $story->getSkipTurnDuration();
        $numSkips = $this->counter->countBetween($updatedAt, $now, $duration);

        // next = most recent contributor + num skips + 1
        $next = $lastContributorIndex + $numSkips + 1;

        // in case it's done a full round of skips and/or gone to the next round
        $next = $next % count($participants);

        return $participants[$next];
    }
}
