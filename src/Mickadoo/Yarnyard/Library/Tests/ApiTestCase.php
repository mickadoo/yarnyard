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
    protected $userWithToken;

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
    protected function getUserByUsername($username)
    {
        return self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getRepository('MickadooYarnyardUserBundle:User')
            ->findOneBy(['username' => $username]);
    }

    protected function getUserWithToken()
    {
        if (!$this->userWithToken) {
            $this->userWithToken = $this->createUserWithToken();
        }

        return $this->userWithToken;
    }

    /**
     * @return User
     * @throws YarnyardException
     */
    private function createUserWithToken()
    {
        $client = static::createClient();

        // create user
        $userDetails = [
            'username' => 'mickadoo',
            'email' => 'michaeldevery@gmail.com',
            'password' => 'user123'
        ];

        $crawler = $client->request(
            'POST',
            'user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($userDetails)
        );

        $response = $client->getResponse();
        if ($response->getStatusCode() !== Codes::HTTP_CREATED) {
            throw new YarnyardException('Error, user creation failed in test');
        }

        // confirm e-mail
        $user = $this->getUserByUsername('mickadoo');
        $confirmationToken = static::$kernel
            ->getContainer()
            ->get('yarnyard.auth.confirmation_token.repository')
            ->findOneBy(['user' => $user]);

        $crawler = $client->request(
            'GET',
            '/user/' . $user->getId() . '/confirm-mail',
            [RequestParameter::TOKEN => $confirmationToken->getToken()]
        );
        $response = $client->getResponse();

        if ($response->getStatusCode() !== Codes::HTTP_OK) {
            throw new YarnyardException('Error, user email confirmation request failed');
        }

        // login
        $oauthClient = self::$kernel->getContainer()->get('fos_oauth_server.client_manager')->findClientBy(['id' => 1]);

        $clientId = $oauthClient->getPublicId();
        $clientSecret  = $oauthClient->getSecret();
        $grantType = 'password';
        $username = $userDetails['username'];
        $password = $userDetails['password'];
        $loginRoute = '/oauth/v2/token';

        $params = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => $grantType,
            'username' => $username,
            'password' => $password
        ];

        $crawler = $client->request('GET', $loginRoute, $params);
        $response = $client->getResponse();

        if ($response->getStatusCode() !== Codes::HTTP_OK) {
            throw new YarnyardException('Error, user login in test failed');
        }

        return $user;
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

    protected function createAuthorizedClient()
    {
        $user = $this->getUserWithToken();

        $token = static::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getRepository('MickadooYarnyardAuthBundle:AccessToken')
            ->findOneBy(['user' => $user]);

        return static::createClient(['environment'=>'test'], ['HTTP_AUTHORIZATION' => 'Bearer '. $token->getToken()]);
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
