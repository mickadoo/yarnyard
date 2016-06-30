<?php

namespace YarnyardBundle\Service;

use YarnyardBundle\Entity\ParticipationGrantRepository;
use YarnyardBundle\Entity\Story;
use YarnyardBundle\Exception\NoAvailableUserException;

class RandomParticipantAdder
{
    /**
     * @var ParticipantLimitCalculator
     */
    protected $calculator;

    /**
     * @var ParticipationGrantRepository
     */
    protected $participantRepo;

    /**
     * @var RandomParticipantFinder
     */
    protected $participantFinder;

    /**
     * @var ParticipationGrantCreator
     */
    protected $grantCreator;

    /**
     * @param ParticipantLimitCalculator   $calculator
     * @param ParticipationGrantRepository $participantRepo
     * @param RandomParticipantFinder      $participantFinder
     * @param ParticipationGrantCreator    $grantCreator
     */
    public function __construct(
        ParticipantLimitCalculator $calculator,
        ParticipationGrantRepository $participantRepo,
        RandomParticipantFinder $participantFinder,
        ParticipationGrantCreator $grantCreator
    ) {
        $this->calculator = $calculator;
        $this->participantRepo = $participantRepo;
        $this->participantFinder = $participantFinder;
        $this->grantCreator = $grantCreator;
    }

    /**
     * @param Story $story
     *
     * @return bool
     */
    public function shouldAddParticipant(Story $story) : bool
    {
        if (!$story->isRandom()) {
            return false;
        }

        $limit = $this->calculator->getLimit($story);
        $numParticipants = $this->participantRepo->getCount($story);

        // if number of current participants is less than the limit
        return $numParticipants < $limit;
    }

    /**
     * @param Story $story
     */
    public function addRandomParticipant(Story $story)
    {
        try {
            $participant = $this->participantFinder->find($story);
        } catch (NoAvailableUserException $exception) {
            // if there are no more random participants available
            // to add then do nothing
            return;
        }

        $this->grantCreator->create($story, $participant);
    }
}
