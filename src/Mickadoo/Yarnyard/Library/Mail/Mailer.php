<?php

namespace Mickadoo\Yarnyard\Library\Mail;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Mailer extends \Swift_Mailer implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param \Swift_Mime_Message|AbstractMail $message
     * @param null $failedRecipients
     * @return int
     */
    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $message->setBody(
            $this->container->get('templating')->render($message->getMailTemplate(), $message->getData()),
            'text/html'
        );

        return parent::send($message, $failedRecipients);
    }
}
