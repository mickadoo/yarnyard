<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Mickadoo\Yarnyard\Library\Controller\RequestParameter;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends RestController
{
    /**
     * @param Request $request
     * @throws YarnyardException
     * @return null
     *
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("confirm-email")
     */
    public function postConfirmationTokenAcceptedAction(Request $request)
    {
        $tokenString = $request->request->get(RequestParameter::TOKEN);
        $userId = $request->request->get(RequestParameter::USER);

        $this->get('confirmation_token.service')->confirmToken($userId, $tokenString);

        return null;
    }
}
