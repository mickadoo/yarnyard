<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Mail\MailClass;

use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationToken;
use Mickadoo\Yarnyard\Library\Mail\AbstractMail;

class EmailConfirmationMail extends AbstractMail
{

    /**
     * @var ConfirmationToken
     */
    protected $confirmationToken;

    /**
     * @param ConfirmationToken $confirmationToken
     */
    public function __construct(ConfirmationToken $confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
        parent::__construct($confirmationToken->getUser());
    }

    /**
     * @return string
     */
    public function getMailSubject()
    {
        return 'MAIL.USER.CONFIRM_EMAIL';
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'token' => $this->confirmationToken->getToken(),
            'frontendUrl' => 'http://localhost:8000/app', // todo do this properly
            'user' => [
                'id' => $this->confirmationToken->getUser()->getId()
            ]
        ];
    }
}
