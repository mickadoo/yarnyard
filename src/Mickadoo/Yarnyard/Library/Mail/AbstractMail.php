<?php

namespace Mickadoo\Yarnyard\Library\Mail;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

abstract class AbstractMail extends \Swift_Message implements MailInterface
{
    /**
     * @param User $receiver
     */
    public function __construct(User $receiver)
    {
        parent::__construct();
        $this->setTo($receiver->getEmail(), $receiver->getUsername());
        $this->setFrom('michael@michaeldevery.com'); // todo move to config
        $this->setSubject($this->getMailSubject());
    }

    /**
     * @return string
     */
    public function getMailTemplate()
    {
        $reflection = new \ReflectionClass($this);
        $directory = dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR;
        $className = $reflection->getShortName();

        $templateName = $directory . '../View/' . $className . '.html.twig';
        $realPath = realpath($templateName);

        return $realPath;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [];
    }
}
