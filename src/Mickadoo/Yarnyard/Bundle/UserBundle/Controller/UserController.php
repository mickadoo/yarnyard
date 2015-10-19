<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @Rest\Route("users")
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
     * @Rest\Route("users/{id}")
     */
    public function getUserAction(User $user)
    {
        return $user;
    }

    /**
     * @param Request $request
     * @return User
     *
     * @ApiDoc()
     *
     * @Rest\View(statusCode=201)
     * @Rest\Route("users")
     */
    public function postUserAction(Request $request)
    {
        $username = $request->request->get('username');
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        return $this->get('user.service')->create($username, $email, $password);
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
     * @Rest\Route("users/{id}")
     */
    public function putUserAction(User $user, Request $request)
    {
        $username = $request->request->get('username');
        $email = $request->request->get('email');

        return $this->get('user.service')->update($user, $username, $email);
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
     * @Rest\Route("users/{id}")
     *
     * @deprecated
     */
    public function patchUserAction(User $user, Request $request)
    {
        // todo decide whether to use PATCH or not
        $this->container->get('patcher')->patch($user, $request->request->all());
        $this->getUserRepository()->update($user);

        return $user;
    }
}
