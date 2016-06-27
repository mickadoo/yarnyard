<?php

namespace YarnyardBundle\Util\DateInterval;

class IntervalCounter
{
    /**
     * @param \DateTime     $start
     * @param \DateTime     $end
     * @param \DateInterval $interval
     *
     * @return int
     */
    public function countBetween(
        \DateTime $start,
        \DateTime $end,
        \DateInterval $interval
    ) : int {
        $count = 0;
        $start = clone $start;
        $end = clone $end;

        // counting starts after one interval has passed
        $start->add($interval);

        while ($start < $end) {
            $start->add($interval);
            ++$count;
        }

        return $count;
    }
}
