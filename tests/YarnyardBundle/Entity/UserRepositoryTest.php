<?php

namespace Tests\YarnyardBundle\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use YarnyardBundle\Exception\YarnyardException;

class UserRepositoryTest extends WebTestCase
{
    /**
     * @test
     *
     * @throws YarnyardException
     */
    public function getRandomWillWork()
    {
        $repo = $this->getContainer()->get('user.repository');
        $count = $repo->getCount();
        $found = [];

        for ($i = 0; $i < $count; ++$i) {
            $user = $repo->findRandom($found);
            $this->assertNotContains($user->getId(), $found);
            $found[] = $user->getId();
        }

        $this->assertEquals($count, count($found));
    }
}
