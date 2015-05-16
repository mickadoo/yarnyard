<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Controller;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @ParamConverter("user", converter="fos_rest.request_body")
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

        $this->get('yarnyard.user.user.repository')->save($user);

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
