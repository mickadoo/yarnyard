<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Controller;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class UserController extends RestController
{

    /**
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("user")
     *
     * @param Request $request
     * @return User[]
     */
    public function getAllUsersAction(Request $request)
    {
        $query = $this->getUserRepository()->createQueryBuilder('user');

        return $this->paginate($request, $query);
    }

    /**
     *
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("user/{id}")
     *
     * @param User $user
     * @return User
     */
    public function getUserAction(User $user)
    {
        return $user;
    }

    /**
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("user")
     *
     * @ParamConverter(converter="fos_rest.request_body", class="MickadooYarnyardUserBundle:User")
     *
     * @param User $user
     * @return User
     */
    public function postUserAction(User $user)
    {
        $validator = $this->get('yarnyard.user.user.post_user_validator');
        $validator->setUser($user);

        if (! $validator->isValid()) {
            return $this->createResponseFromValidator($validator);
        }

        $user->setSalt(uniqid(mt_rand(), true));
        $encoder = $this->get('security.password_encoder');
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

        $this->getUserRepository()->save($user);

        return $user;
    }

    /**
     * @ApiDoc()
     *
     * @Rest\View()
     * @Rest\Route("user/{id}")
     *
     * @ParamConverter(class="MickadooYarnyardUserBundle:User")
     *
     * @param User $user
     * @param Request $request
     * @return User
     */
    public function patchUserAction(User $user, Request $request)
    {
        $validator = $this->get('yarnyard.user.user.patch_user_validator');
        $validator->setUser($user);

        if (! $validator->isValid()) {
            return $this->createResponseFromValidator($validator);
        }

        $this->setPropertiesFromRequest($user, $request);
        $this->getUserRepository()->update($user);

        return $user;
    }

}
