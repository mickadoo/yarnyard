<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Controller;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UserController extends Controller
{
    /**
     * @ApiDoc(
     *  description="Get a user",
     *  section="user"
     * )
     *
     * @Rest\View()
     * @Rest\Route("user/{id}")
     *
     * @ParamConverter("user", class="User")
     *
     * @param User $user
     * @return User
     */
    public function getUserAction(User $user)
    {
        return $user;
    }

    /**
     * @ApiDoc(
     *  description="Add a new user",
     *  section="user"
     * )
     *
     * @Rest\View()
     * @Rest\Route("user")
     *
     * @ParamConverter("user", class="User", converter="user_param_converter")
     *
     * @param User $user
     * @return User
     */
    public function postUserAction(User $user)
    {
        return $user;
    }

}
