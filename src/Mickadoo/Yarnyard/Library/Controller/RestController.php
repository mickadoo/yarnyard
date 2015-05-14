<?php


namespace Mickadoo\Yarnyard\Library\Controller;


use FOS\RestBundle\Controller\FOSRestController;

class RestController extends FOSRestController
{

    public function getUser()
    {
        return $this->container->get('security.context')->getToken()->getUser();
    }

}