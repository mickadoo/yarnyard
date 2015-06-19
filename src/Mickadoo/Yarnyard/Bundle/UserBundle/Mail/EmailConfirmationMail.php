<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Mail;

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
     * @return \Swift_Mime_MimePart
     */
    public function getMailBody()
    {
        return 'TODO implement body :' . $this->confirmationToken->getToken();
    }

    public function getMailSubject()
    {
        return 'MAIL.USER.CONFIRM_EMAIL';
    }

}