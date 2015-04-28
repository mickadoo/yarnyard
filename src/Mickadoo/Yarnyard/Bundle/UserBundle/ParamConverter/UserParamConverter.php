<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\ParamConverter;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class UserParamConverter implements ParamConverterInterface {

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $user = new User();
        $user->setUsername("mickadoo");
        $user->setEmail("michaeldevery@gmail.com");

        $request->attributes->set($configuration->getName(), $user);

        return true;
    }

    /**
     * @param ParamConverter $configuration
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === "User";
    }

}