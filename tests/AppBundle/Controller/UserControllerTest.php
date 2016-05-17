<?php

namespace Tests\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use YarnyardBundle\DataFixtures\ORM\LoadUserData;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Test\ApiTestCase;

class UserControllerTest extends ApiTestCase
{
    /**
     * @test
     */
    public function getUserWillReturnSpecificUser()
    {
        $this->loadFixtures([LoadUserData::class]);
        $client = $this->createClient();

        /** @var User $user */
        $user = static::$kernel->getContainer()->get('user.repository')->findOneBy([]);

        $client->request(Request::METHOD_GET, '/users/' . $user->getId());
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($user->getId(), json_decode($response->getContent())->id);
    }
}
