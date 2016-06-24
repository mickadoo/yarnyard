<?php

namespace YarnyardBundle\Service;

use YarnyardBundle\Entity\Story;

class ParticipantLimitCalculator
{
    /**
     * @param Story $randomStory
     *
     * @return int
     */
    public function getLimit(Story $randomStory) : int
    {
        return $randomStory->getId() % 9 + 5; // min 5, max 14
    }
}
