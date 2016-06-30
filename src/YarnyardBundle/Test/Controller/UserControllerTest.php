<?php

namespace YarnyardBundle\Test\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use YarnyardBundle\DataFixtures\ORM\LoadUserData;
use YarnyardBundle\Entity\User;

class UserControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function getUserWillReturnSpecificUser()
    {
        $this->loadFixtures([LoadUserData::class]);
        $client = $this->createClient();

        /** @var User $user */
        $user = $this->getContainer()->get('user.repository')->findOneBy([]);

        $client->request(Request::METHOD_GET, '/users/'.$user->getId());
        $response = $client->getResponse();
        $returnedId = json_decode($response->getContent())->id;

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($user->getId(), $returnedId);
    }

    /**
     * @test
     */
    public function getAllUsersWillReturnArray()
    {
        $this->loadFixtures([LoadUserData::class]);
        $client = $this->createClient();

        $client->request(Request::METHOD_GET, '/users');
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue(is_array(json_decode($response->getContent())));
    }
}
