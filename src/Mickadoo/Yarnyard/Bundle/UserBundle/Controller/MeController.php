<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Controller;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\Controller\RestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;

class MeController extends RestController
{

    /**
     * @ApiDoc()
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