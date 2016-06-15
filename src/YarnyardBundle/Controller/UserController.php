<?php

namespace YarnyardBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use YarnyardBundle\Entity\User;

class UserController extends AbstractRestController
{
    /**
     * @ApiDoc()
     *
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Route("users")
     *
     * @ParamConverter("query", options={"class"="YarnyardBundle\Entity\User"})
     *
     * @param Request      $request
     * @param QueryBuilder $query
     *
     * @return User[]
     */
    public function getAllUsersAction(Request $request, QueryBuilder $query)
    {
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
