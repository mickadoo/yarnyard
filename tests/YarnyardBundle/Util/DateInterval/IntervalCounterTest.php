<?php

namespace Tests\YarnyardBundle\Util\DateInterval;

use YarnyardBundle\Util\DateInterval\IntervalCounter;

class IntervalCounterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IntervalCounter
     */
    protected $counter;

    /**
     * @test
     * @dataProvider countBetweenProvider
     *
     * @param \DateTime     $start
     * @param \DateTime     $end
     * @param \DateInterval $interval
     * @param               $expected
     */
    public function countBetweenWillWork(
        \DateTime $start,
        \DateTime $end,
        \DateInterval $interval,
        $expected
    ) {
        $result = $this->counter->countBetween($start, $end, $interval);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function countBetweenProvider()
    {
        return [
            [
                new \DateTime('yesterday'),
                new \DateTime('tomorrow'),
                new \DateInterval('P1D'),
                1,
            ],
            [
                new \DateTime('-10 days'),
                new \DateTime('+ 21 days'),
                new \DateInterval('P1D'),
                30,
            ],
            [
                new \DateTime('-10 minutes'),
                new \DateTime('+ 1 hour'),
                new \DateInterval('PT1M'),
                69,
            ],
            [
                new \DateTime('-1 week'),
                new \DateTime('tomorrow'),
                new \DateInterval('P1D'),
                7,
            ],
        ];
    }

    protected function setUp()
    {
        $this->counter = new IntervalCounter();
    }
}
