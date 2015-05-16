<?php

namespace Mickadoo\Yarnyard\Library\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RestController extends FOSRestController
{

    /**
     * @return User|null
     */
    public function getUser()
    {
        $token = $this->container->get('security.token_storage')->getToken();

        return $token->getUser();
    }

    /**
     * @param $route
     * @return Response
     */
    public function handleSubRequest($route)
    {
        $_SERVER['REQUEST_URI'] = $route;
        $request = Request::createFromGlobals();
        $response = $this->get('kernel')->handle($request, HttpKernelInterface::SUB_REQUEST);

        return $response;
    }

}