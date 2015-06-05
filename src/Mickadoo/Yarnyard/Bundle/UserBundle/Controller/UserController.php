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
     * @ApiDoc(
     *  description="Get all users",
     *  section="user"
     * )
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
     * @Rest\View()
     * @Rest\Route("user/{id}")
     *
     * @ApiDoc()
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
     * description="Add a new user",
     * section="user",
     * requirements={
     *      {
     *          "name"="email",
     *          "dataType"="string",
     *          "requirement"="\w+",
     *          "description"="email address"
     *      },
     *      {
     *          "name"="username",
     *          "dataType"="string",
     *          "requirement"="\w+",
     *          "description"="username for new user"
     *      },
     *      {
     *          "name"="password",
     *          "dataType"="string",
     *          "requirement"="\w+",
     *          "description"="password for new user"
     *      }
     *  }
     * )
     *
     * @Rest\View()
     * @Rest\Route("user")
     *
     * @ParamConverter("user", converter="fos_rest.request_body", class="Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User")
     *
     * @param User $user
     * @return User
     */
    public function postUserAction(User $user)
    {
        $validator = $this->get('yarnyard.user.user.validator');
        $validator->setUser($user);

        if (! $validator->isValid()) {
            return $this->createResponseFromValidator($validator);
        }

        $user->setSalt(uniqid(mt_rand(), true));
        $encoder = $this->get('security.password_encoder');
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

        $this->get('yarnyard.user.user.repository')->save($user);

        return $user;
    }

    /**
     * @ApiDoc(
     * description="Update a user",
     * section="user",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="int",
     *          "requirement"="\d+",
     *          "description"="user id"
     *      }
     *  }
     * )
     *
     *
     *
     * @Rest\View()
     * @Rest\Route("user/{id}")
     *
     * @ParamConverter("user", converter="fos_rest.request_body", class="Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User")
     *
     * @param User $user
     * @return User
     */
    public function patchUserAction(User $user)
    {

    }



}
