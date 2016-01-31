<?php

namespace Tests\AppBundle\Controller;

use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Test\ApiTestCase;

class UserControllerTest extends ApiTestCase
{
    /**
     * @test
     */
    public function getUserWillReturnSpecificUser()
    {
        $client = static::createClient();
        /** @var User $user */
        $user = static::$kernel->getContainer()->get('user.repository')->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            '/users/' . $user->getId()
        );

        $response = $client->getResponse();

        $this->assertEquals(Codes::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($user->getId(), json_decode($response->getContent())->id);
    }
}
