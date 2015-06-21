<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\Roles;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\Controller\RequestParameter;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends RestController
{

    /**
     * @param Request $request
     * @param User $user
     *
     * @return User
     *
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Get("user/{id}/confirm-mail")
     *
     * @ParamConverter(class="Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User")
     */
    public function postConfirmationTokenAcceptedAction(Request $request, User $user)
    {
        // todo: when frontend exists this should be real POST and email link should point to frontend page
        $tokenString = $request->query->get(RequestParameter::TOKEN);
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
