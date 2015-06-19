<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle;

use Mickadoo\Yarnyard\Bundle\UserBundle\Event\UserSubscriber;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MickadooYarnyardUserBundle extends Bundle
{

    public function boot()
    {
        // register subscribers
        $this->container->get('event_dispatcher')->addSubscriber(new UserSubscriber($this->container));
    }

}
