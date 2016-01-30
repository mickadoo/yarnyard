<?php

namespace YarnyardBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mickadoo\Yarnyard\Library\EntityHelper\FixtureReference;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $kevin = new User();
        $kevin
            ->setUsername('kevin')
            ->setEmail('michaeldevery+kevin@gmail.com')
            ->setSalt(uniqid(mt_rand(), true))
            ->setPassword('user123');

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
