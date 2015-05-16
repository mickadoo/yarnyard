<?php

namespace Mickadoo\Yarnyard\Library\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\EntityRepository\RepositoryTrait;
use Mickadoo\Yarnyard\Library\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RestController extends FOSRestController
{

    use RepositoryTrait;

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->getToken()->getUser();
    }

    /**
     * @return null|TokenInterface
     */
    public function getToken()
    {
        return $this->container->get('security.token_storage')->getToken();
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

    /**
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function createResponseFromValidator(ValidatorInterface $validator)
    {
        $response = new Response();
        $response->setStatusCode($validator->getErrorCode());
        $responseBody = [
            'error' =>
                [
                    'key' => $validator->getErrorKey()
                ]
        ];
        $response->setContent(json_encode($responseBody));

        return $response;
    }

}