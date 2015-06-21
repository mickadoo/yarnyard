<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\AccessToken;
use Mickadoo\Yarnyard\Library\EntityHelper\FixtureReference;

class LoadAccessTokenData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $token = new AccessToken();
        $tomorrow = new \DateTime('tomorrow');
        $token
            ->setClient($this->getReference(FixtureReference::CLIENT))
            ->setUser($this->getReference(FixtureReference::KEVIN))
            ->setToken('YTRmOTQ1ODVkODE4N2UxMWY3ZjMyOTUyMWU3ZDIzYjc0OWI0Nzc3NzBkOGVhZGY4NTVmODgyMmY4MWZkNjQ0MA')
            ->setScope('user')
            ->setExpiresAt($tomorrow->getTimeStamp());

        $manager->persist($token);
        $manager->flush($token);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return FixtureReference::ORDER_TOKEN;
    }


}
