<?php

namespace YarnyardBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use YarnyardBundle\Entity\User;

class UserController extends AbstractRestController
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
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Route("users")
     */
    public function getAllUsersAction(Request $request)
    {
        $query = $this->get('user.repository')->createQueryBuilder('user');

        return $this->paginate($request, $query);
    }

    /**
     * @param User $user The target user
     *
     * @return User
     *
     * @ApiDoc()
     *
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Route("users/{id}")
     */
    public function getUserAction(User $user)
    {
        return $user;
    }
}
