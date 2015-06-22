<?php

namespace Mickadoo\Yarnyard\Library\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\Instantiator\Exception\InvalidArgumentException;
use FOS\RestBundle\Util\Codes;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\Controller\RequestParameter;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Kernel;

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
     * @var array
     */
    protected $testUserDetails = [
        'username' => 'mickadoo',
        'email' => 'michaeldevery@gmail.com',
        'password' => 'user123'
    ];

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
     * @param $username
     * @return User
     */
    private function getUserByUsername($username)
    {
        return self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getRepository('MickadooYarnyardUserBundle:User')
            ->findOneBy(['username' => $username]);
    }

    protected function getLoggedInUser()
    {
        if (!$this->loggedInUser) {
            $this->loggedInUser = $this->createUserWithToken();
        }

        return $this->loggedInUser;
    }

    /**
     * @return User
     * @throws YarnyardException
     */
    private function createUserWithToken()
    {
        $unauthorizedClient = static::createClient();
        $user = $this->postUser($unauthorizedClient, $this->testUserDetails);
        $this->confirmEmailAddress($unauthorizedClient, $user);
        $this->loginUser($unauthorizedClient, $user);

        return $user;
    }

    protected function postUser(Client $client, array $userDetails)
    {
        $client->request(
            'POST',
            'user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($userDetails)
        );

        $response = $client->getResponse();

        if ($response->getStatusCode() !== Codes::HTTP_CREATED) {
            throw new YarnyardException('Error, user creation failed');
        }

        return $this->getUserByUsername('mickadoo');
    }

    /**
     * @param User $user
     * @throws YarnyardException
     */
    private function confirmEmailAddress(Client $client, User $user)
    {
        $confirmationToken = static::$kernel
            ->getContainer()
            ->get('yarnyard.auth.confirmation_token.repository')
            ->findOneBy(['user' => $user]);

        $client->request(
            'GET',
            '/user/' . $user->getId() . '/confirm-mail',
            [RequestParameter::TOKEN => $confirmationToken->getToken()]
        );
        $response = $client->getResponse();

        if ($response->getStatusCode() !== Codes::HTTP_OK) {
            throw new YarnyardException('Error, user email confirmation request failed');
        }
    }

    private function loginUser(Client $client, User $user)
    {
        $oauthClient = self::$kernel->getContainer()->get('fos_oauth_server.client_manager')->findClientBy(['id' => 1]);

        $clientId = $oauthClient->getPublicId();
        $clientSecret  = $oauthClient->getSecret();
        $grantType = 'password';
        $loginRoute = '/oauth/v2/token';

        $params = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => $grantType,
            'username' => $user->getUsername(),
            'password' => $this->testUserDetails['password']
        ];

        $client->request('GET', $loginRoute, $params);
        $response = $client->getResponse();

        if ($response->getStatusCode() !== Codes::HTTP_OK) {
            throw new YarnyardException('Error, user login in test failed');
        }
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
     * Load fixtures for all bundles
     *
     * @param Kernel $kernel
     */
    private static function loadFixtures(Kernel $kernel)
    {
        $loader = new DataFixturesLoader($kernel->getContainer());
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
