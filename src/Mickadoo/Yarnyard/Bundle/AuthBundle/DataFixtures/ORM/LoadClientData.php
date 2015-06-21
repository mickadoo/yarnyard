<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\Client;
use Mickadoo\Yarnyard\Library\EntityHelper\FixtureReference;

class LoadClientData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $grantTypes = ['authorization_code', 'password', 'refresh-token', 'token', 'client-credentials'];
        $client
            ->setAllowedGrantTypes($grantTypes)
            ->setRedirectUris(['api.yarnyard.dev']);

        $manager->persist($client);
        $manager->flush($client);

        $this->setReference(FixtureReference::CLIENT, $client);
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return FixtureReference::ORDER_CLIENT;
    }


}
