<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\AccessToken;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationToken;
use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\Roles;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
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
     * @return User
     *
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("confirm-email")
     *
     */
    public function postConfirmationTokenAcceptedAction(Request $request)
    {
        $tokenString = $request->request->get(RequestParameter::TOKEN);
        $userId = $request->request->get(RequestParameter::USER);

        if (!$tokenString || !$userId) {
            throw new YarnyardException('userId or token not set in request');
        }

        /** @var User $user */
        $user = $this->getUserRepository()->find($userId);

        if (! $user) {
            return null;
        }

        /** @var ConfirmationToken $token */
        $token = $this->getConfirmationTokenRepository()->findOneBy([
            'user' => $user,
            'token' => $tokenString
        ]);

        if ($token) {
            $this->getRoleRepository()->addRoleForUser($user, Roles::ACTIVE_USER);
            $this->getConfirmationTokenRepository()->delete($token);
        }

        return $user;
    }
}
