<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Event;

use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\UserEvents;
use Mickadoo\Yarnyard\Bundle\UserBundle\Mail\EmailConfirmationMail;
use Mickadoo\Yarnyard\Library\Subscriber\AbstractContainerAwareSubscriber;
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
        $confirmEmailMail = new EmailConfirmationMail($event->getConfirmationToken());
        $this->getContainer()->get('mailer')->send($confirmEmailMail);
    }

}