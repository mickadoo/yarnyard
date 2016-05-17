<?php

namespace YarnyardBundle\Test;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use YarnyardBundle\Entity\User;

abstract class ApiTestCase extends WebTestCase
{
    /**
     * @var Connection
     */
    protected static $connection;

    /**
     * @var User
     */
    protected $loggedInUser;

    /**
     * always create and boot kernel.
     */
    protected function setUp()
    {
        self::createKernel();
        self::bootKernel();
        self::loadFixtures(self::$kernel);
    }

    /**
     * @param array $options
     */
    protected static function bootKernel(array $options = array())
    {
        parent::bootKernel($options);

        if (!self::$connection) {
            self::$connection = self::$kernel->getContainer()->get('doctrine.dbal.default_connection');
        }

        self::$kernel->getContainer()->set('doctrine.dbal.default_connection', self::$connection);
    }

    /**
     * @param KernelInterface $kernel
     */
    private static function loadFixtures(KernelInterface $kernel)
    {
        // todo load fixtures based on test
        $loader = new DataFixturesLoader($kernel->getContainer());
        /** @var EntityManager $em */
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        foreach ($kernel->getBundles() as $bundle) {
            $path = $bundle->getPath() . '/DataFixtures/ORM';

            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }

        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new InvalidArgumentException('Could not find any fixtures to load in');
        }
        $purger = new ORMPurger($em);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures);
    }

    /**
     * @return User|UserInterface
     */
    protected function getLoggedInUser()
    {
        // todo
    }

    /**
     * @return Client
     */
    protected function getAuthorizedClient()
    {
        // todo
    }
}
