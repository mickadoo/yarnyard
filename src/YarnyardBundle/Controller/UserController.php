<?php

namespace YarnyardBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use YarnyardBundle\Entity\User;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Security("has_role('1')")
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
}
