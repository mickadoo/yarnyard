<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Validator;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

class PostUserValidator extends UserValidator
{

    /**
     * @return bool
     */
    protected function isUserEmailValid()
    {
        $email = $this->user->getEmail();

        if (count($this->getUserRepository()->findBy([User::USER_FIELD_EMAIL => $email])) > 0) {
            $this->setErrorResponse(self::ERROR_USER_EMAIL_ALREADY_EXISTS);

            return false;
        }

        return parent::isUserEmailValid($email);
    }

    /**
     * @return bool
     */
    protected function isUserUsernameValid()
    {
        $username = $this->user->getUsername();

        if (count($this->getUserRepository()->findBy([User::USER_FIELD_USERNAME => $username])) > 0) {
            $this->setErrorResponse(self::ERROR_USER_USERNAME_ALREADY_EXISTS);

            return false;
        }

        return parent::isUserUsernameValid($username);
    }

    /**
     * @return bool
     */
    protected function isUserPasswordValid()
    {
        $password = $this->user->getPassword();

        if (strlen($password) > 55) {
            $this->setErrorResponse(self::ERROR_USER_PASSWORD_TOO_LONG);

            return false;
        }

        return parent::isUserPasswordValid();
    }

}