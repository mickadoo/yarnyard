<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Event;

use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\UserEvents;
use Mickadoo\Yarnyard\Bundle\UserBundle\Mail\EmailConfirmationMail;
use Mickadoo\Yarnyard\Library\Subscriber\ContainerAwareSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber extends ContainerAwareSubscriber implements EventSubscriberInterface
{

    /**
     * Get the events subscribed to and their order
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            UserEvents::USER_CREATED => [
                array('onUserCreated', 0)
            ]
        ];
    }

    /**
     * Send the e-mail confirmation mail to the new user
     *
     * @param UserCreatedEvent $event
     *
     * @return void
     */
    public function onUserCreated(UserCreatedEvent $event)
    {
        $confirmationMail = new EmailConfirmationMail($event->getConfirmationToken());

        $this->getContainer()->get('mailer')->send($confirmationMail);
    }
}
