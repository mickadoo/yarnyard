<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;
use Mickadoo\Yarnyard\Library\Tests\ApiTestCase;

class UserControllerTest extends ApiTestCase
{
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
