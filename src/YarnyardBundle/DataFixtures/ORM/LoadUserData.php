<?php

namespace YarnyardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use YarnyardBundle\DataFixtures\Constants\FixtureReference;
use YarnyardBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $users = ['kevin', 'philip', 'mary', 'ann'];

        foreach ($users as $userName) {
            $user = new User('userUuid'.ucfirst($userName));
            $manager->persist($user);
            $this->setReference($userName, $user);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return FixtureReference::ORDER_USER;
    }
}
