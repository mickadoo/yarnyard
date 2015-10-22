<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationToken;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Bundle\UserBundle\Mail\MailClass\EmailConfirmationMail;
use Mickadoo\Yarnyard\Library\Controller\RequestParameter;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
use Mickadoo\Yarnyard\Library\Mail\AbstractMail;
use Mickadoo\Yarnyard\Library\Tests\ApiTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends ApiTestCase
{

    /**
     * @param array $userDetails
     * @throws YarnyardException
     * @dataProvider userDetailsDataProvider
     */
    public function testPostUser(array $userDetails)
    {
        $client = static::createClient();
        $client->enableProfiler();

        $client->request(
            Request::METHOD_POST,
            'users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($userDetails)
        );

        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('username', $responseContent);
        $this->assertEquals($userDetails['username'], $responseContent['username']);

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());

        /** @var AbstractMail $mail */
        $mail = $mailCollector->getMessages()[0];

        $this->assertTrue($mail instanceof EmailConfirmationMail);
        $this->assertContains(
            $this->getConfirmationTokenByUser(
                $this->getUserByUsername($userDetails['username'])
            )->getToken(),
            $mail->getBody()
        );
    }

    /**
     * @param array $userDetails
     * @throws YarnyardException
     * @dataProvider userDetailsDataProvider
     */
    public function testUserCreationAndLoginFlow(array $userDetails)
    {
        $client = static::createClient();

        $client->request(
            Request::METHOD_POST,
            'users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($userDetails)
        );

        $username = json_decode($client->getResponse()->getContent(), true)['username'];
        $user = $this->getUserByUsername($username);

        $confirmationToken = $this->getConfirmationTokenByUser($user);

        $client->request(
            Request::METHOD_POST,
            '/confirm-email',
            [
                RequestParameter::TOKEN => $confirmationToken->getToken(),
                RequestParameter::USER => $user->getId()
            ]
        );

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
            'password' => $userDetails['password']
        ];

        $client->request('GET', $loginRoute, $params);
        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());
    }

    /**
     * Get all users and check if the logged-in user is part of them
     */
    public function testGetAllUsers()
    {
        $client = $this->getAuthorizedClient();
        $client->request('GET', '/users');
        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true);
        $this->assertTrue(is_array($responseContent));

        $usernames = [];
        foreach ($responseContent as $user) {
            $usernames[] = $user['username'];
        }
        $this->assertContains($this->getLoggedInUser()->getUsername(), $usernames);
    }

    /**
     * Get the logged in user from the api
     */
    public function testGetUser()
    {
        $client = $this->getAuthorizedClient();
        $client->request('GET', '/users/' . $this->getLoggedInUser()->getId());

        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('username', $responseContent);
        $this->assertEquals($this->getLoggedInUser()->getUsername(), $responseContent['username']);
    }

    /**
     * Update a user's username and check if update succeeded
     */
    public function testPutUser()
    {
        $newUsername = 'philip';

        $data = [
            'username' => $newUsername,
            'email' => $this->getLoggedInUser()->getEmail()
        ];

        $client = $this->getAuthorizedClient();
        $client->request(
            Request::METHOD_PUT,
            '/users/' .$this->getLoggedInUser()->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($data)
        );
        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true);
        $this->assertEquals($newUsername, $responseContent['username']);
        $this->assertEquals($this->getLoggedInUser()->getEmail(), $responseContent['email']);
    }

    /**
     * @return array
     */
    public function userDetailsDataProvider()
    {
        return [
            [
                [
                    'username' => 'mickadoo',
                    'email' => 'michaeldevery@gmail.com',
                    'password' => 'user123'
                ]
            ]
        ];
    }

    /**
     * @param User $user
     * @return ConfirmationToken|object
     */
    protected function getConfirmationTokenByUser(User $user)
    {
        return static::$kernel
            ->getContainer()
            ->get('yarnyard.auth.confirmation_token.repository')
            ->findOneBy(['user' => $user]);
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
}
