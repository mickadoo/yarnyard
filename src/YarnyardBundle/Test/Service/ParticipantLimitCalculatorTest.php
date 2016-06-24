<?php

namespace YarnyardBundle\Test\Service;

use YarnyardBundle\Entity\Story;
use YarnyardBundle\Service\ParticipantLimitCalculator;

class ParticipantLimitCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function limitWillReturnExpectedRange()
    {
        $calculator = new ParticipantLimitCalculator();
        $lastResult = 0;

        foreach (range(1, 20) as $id) {
            $story = $this->prophesize(Story::class);
            $story->getId()->willReturn($id);
            $result = $calculator->getLimit($story->reveal());

            $this->assertTrue($result <= 14 && $result >= 5);
            $this->assertNotEquals($lastResult, $result);

            $lastResult = $result;
        }
    }
}
