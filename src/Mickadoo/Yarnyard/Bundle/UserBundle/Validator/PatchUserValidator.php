<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Validator;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;

class PatchUserValidator extends UserValidator
{

    /**
     * @return bool
     */
    protected function isUserEmailValid()
    {
        $email = $this->getRequest()->request->get(User::USER_FIELD_EMAIL);

        if (count($this->getUserRepository()->findBy([User::USER_FIELD_EMAIL => $email])) > 0) {
            $this->setErrorResponse(self::ERROR_USER_EMAIL_ALREADY_EXISTS);
            return false;
        }

        return parent::isUserEmailValid();
    }

    /**
     * @return bool
     */
    protected function isUserUsernameValid()
    {
        $username = $this->getRequest()->request->get(User::USER_FIELD_USERNAME);

        if (count($this->getUserRepository()->findBy([User::USER_FIELD_USERNAME => $username])) > 0) {
            $this->setErrorResponse(self::ERROR_USER_USERNAME_ALREADY_EXISTS);
            return false;
        }

        return parent::isUserUsernameValid();
    }

    /**
     * @return bool
     */
    protected function isUserPasswordValid()
    {
        $password = $this->getRequest()->request->get(User::USER_FIELD_PASSWORD);

        if ($password && strlen($password) > 55) {
            $this->setErrorResponse(self::ERROR_USER_PASSWORD_TOO_LONG);

            return false;
        }

        return parent::isUserPasswordValid();
    }

}