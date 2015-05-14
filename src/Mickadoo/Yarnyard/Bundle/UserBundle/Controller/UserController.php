<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Controller;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class UserController extends RestController
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
     * @ParamConverter("user", class="\Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User", converter="user_param_converter")
     *
     * @param User $user
     * @return User
     */
    public function postUserAction(User $user)
    {
        $this->get('mickadoo_yarnyard_user.user.repository')->save($user);

        return $user;
    }

    /**
     * @ApiDoc(
     *  description="Get currently logged in user",
     *  section="user"
     * )
     *
     * @Rest\View()
     * @Rest\Route("me")
     *
     * @return User
     */
    public function getMeAction()
    {
        return $this->getUser();
    }

}
