<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use Mickadoo\Yarnyard\Bundle\UserBundle\Mail\MailClass\EmailConfirmationMail;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
use Mickadoo\Yarnyard\Library\Mail\AbstractMail;
use Mickadoo\Yarnyard\Library\Tests\ApiTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;

class UserControllerTest extends ApiTestCase
{

    /**
     * Create a new user
     *
     * @throws YarnyardException
     */
    public function testPostUser()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $this->postUser($client, $this->testUserDetails);
        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('username', $responseContent);
        $this->assertEquals($this->testUserDetails['username'], $responseContent['username']);

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());

        /** @var AbstractMail $mail */
        $mail = $mailCollector->getMessages()[0];

        $this->assertTrue($mail instanceof EmailConfirmationMail);
        $this->assertContains(
            $this->getConfirmationTokenByUser(
                $this->getUserByUsername($this->testUserDetails['username'])
            )->getToken(),
            $mail->getBody()
        );
    }

    /**
     * Get all users and check if the logged-in user is part of them
     */
    public function testGetAllUsers()
    {
        $client = $this->getAuthorizedClient();
        $client->request('GET', '/user');
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
        $client->request('GET', '/user/' . $this->getLoggedInUser()->getId());

        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('username', $responseContent);
        $this->assertEquals($this->getLoggedInUser()->getUsername(), $responseContent['username']);
    }

    /**
     * Update a user's username and check if update succeeded
     */
    public function testPatchUser()
    {
        $newUsername = 'philip';

        $client = $this->getAuthorizedClient();
        $client->request(
            'PATCH',
            '/user/' .$this->getLoggedInUser()->getId(),
            [],
            [],
            [],
            json_encode(['username'=>$newUsername])
        );
        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());

        $responseContent = json_decode($response->getContent(), true);
        $this->assertEquals($newUsername, $responseContent['username']);
        $this->assertEquals($this->getLoggedInUser()->getEmail(), $responseContent['email']);
    }
}
