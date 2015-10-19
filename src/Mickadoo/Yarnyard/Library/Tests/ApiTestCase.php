<?php

namespace Mickadoo\Yarnyard\Library\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\AccessToken;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class ApiTestCase extends WebTestCase
{
    /**
     * @var Connection
     */
    static protected $connection;

    /**
     * @var User
     */
    protected $loggedInUser;

    /**
     * @var Client
     */
    private $authorizedClient;

    /**
     * always create and boot kernel
     */
    protected function setUp()
    {
        self::createKernel();
        self::bootKernel();
        self::loadFixtures(self::$kernel);
    }

    /**
     * @return User|UserInterface
     */
    protected function getLoggedInUser()
    {
        if (!$this->loggedInUser) {
            $token = static::$kernel
                ->getContainer()
                ->get('doctrine')
                ->getRepository('MickadooYarnyardAuthBundle:AccessToken')
                ->findOneBy([]);

            $this->loggedInUser = $token->getUser();
        }

        return $this->loggedInUser;
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
     * @return Client
     */
    protected function getAuthorizedClient()
    {
        if (! $this->authorizedClient) {
            $user = $this->getLoggedInUser();

            $token = static::$kernel
                ->getContainer()
                ->get('doctrine')
                ->getRepository('MickadooYarnyardAuthBundle:AccessToken')
                ->findOneBy(['user' => $user]);

            $this->authorizedClient = static::createClient(
                [],
                ['HTTP_AUTHORIZATION' => 'Bearer '. $token->getToken()]
            );
        }

        return $this->authorizedClient;
    }

    /**
     * @param KernelInterface $kernel
     */
    private static function loadFixtures(KernelInterface $kernel)
    {
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
}
