<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Tests\Controller;

use Mickadoo\Yarnyard\Library\Tests\ApiTestCase;

class UserControllerTest extends ApiTestCase
{
    public function testSimple()
    {
        $client = $this->createAuthorizedClient();

        $user = $this->getUserWithToken();

        $token = static::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getRepository('MickadooYarnyardAuthBundle:AccessToken')
            ->findOneBy(['user' => $this->getUserWithToken()]);

        $tokenString = 'Bearer ' . $token->getToken();
        $authorizationHeaders = ['HTTP_AUTHORIZATION' => $tokenString];

        $crawler = $client->request('GET', '/user', [], [], $authorizationHeaders);
        $request = $client->getRequest();
        $response = $client->getResponse();

        $this->assertTrue(1 === 1);
    }
}
