<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Event;

use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\UserEvents;
use Mickadoo\Yarnyard\Library\Subscriber\AbstractContainerAwareSubscriber;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber extends AbstractContainerAwareSubscriber implements EventSubscriberInterface
{

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::USER_CREATED_EVENT => [
                array('onUserCreated', 0)
            ]
        ];
    }

    /**
     * @param UserCreatedEvent $event
     */
    public function onUserCreated(UserCreatedEvent $event)
    {
        $this->container->get('logger')->log(Logger::ERROR, 'lalalala ' . $event->getUser()->getUsername());
    }

}