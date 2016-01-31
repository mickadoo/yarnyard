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
        $kevin = new User('userUuidKevin');

        $manager->persist($kevin);
        $manager->flush();

        $this->setReference(FixtureReference::KEVIN, $kevin);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return FixtureReference::ORDER_USER;
    }
}
