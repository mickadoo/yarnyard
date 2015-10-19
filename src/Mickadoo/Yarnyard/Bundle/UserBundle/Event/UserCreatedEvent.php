<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Event;

use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationToken;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserCreatedEvent extends Event
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var ConfirmationToken
     */
    protected $confirmationToken;

    /**
     * @param ConfirmationToken $confirmationToken
     */
    function __construct(ConfirmationToken $confirmationToken)
    {
        $this->user = $confirmationToken->getUser();
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return ConfirmationToken
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

}