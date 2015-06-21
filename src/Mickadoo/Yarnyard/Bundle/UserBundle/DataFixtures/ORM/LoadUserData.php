<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\EntityHelper\FixtureReference;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
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
        $manager->flush($kevin);

        $this->setReference(FixtureReference::KEVIN, $kevin);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return FixtureReference::ORDER_USER;
    }

}
