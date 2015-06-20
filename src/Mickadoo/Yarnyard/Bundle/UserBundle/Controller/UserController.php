<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\UserEvents;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Bundle\UserBundle\Event\UserCreatedEvent;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class UserController extends RestController
{

    /**
     * @param Request $request
     *
     * @return User[]
     *
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("user")
     */
    public function getAllUsersAction(Request $request)
    {
        $query = $this->getUserRepository()->createQueryBuilder('user');

        return $this->paginate($request, $query);
    }

    /**
     * @param User $user The target user
     *
     * @return User
     *
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("user/{id}")
     */
    public function getUserAction(User $user)
    {
        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     *
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("user")
     *
     * @ParamConverter(
     *  "user",
     *  converter="fos_rest.request_body",
     *  class="Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User"
     * )
     */
    public function postUserAction(User $user)
    {
        $validator = $this->get('yarnyard.user.user.post_user_validator');
        $validator->setUser($user);

        if (!$validator->isValid()) {
            return $this->createResponseFromValidator($validator);
        }

        $user->setSalt(uniqid(mt_rand(), true));
        $encoder = $this->get('security.password_encoder');
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

        $this->getUserRepository()->save($user);

        $confirmationToken = $this
            ->getConfirmationTokenRepository()
            ->createTokenForUser($user);

        $newUserEvent = new UserCreatedEvent($confirmationToken);

        $this->get('event_dispatcher')->dispatch(UserEvents::USER_CREATED, $newUserEvent);

        return $user;
    }

    /**
     * @param User $user
     * @param Request $request
     *
     * @return User
     *
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("user/{id}")
     *
     * @ParamConverter(class="Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User")
     */
    public function patchUserAction(User $user, Request $request)
    {
        $validator = $this->get('yarnyard.user.user.patch_user_validator');
        $validator->setUser($user);

        if (!$validator->isValid()) {
            return $this->createResponseFromValidator($validator);
        }

        $this->setPropertiesFromRequest($user, $request);
        $this->getUserRepository()->update($user);

        return $user;
    }

}
