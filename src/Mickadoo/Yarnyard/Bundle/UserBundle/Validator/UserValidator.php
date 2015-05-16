<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Validator;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Bundle\UserBundle\ErrorConstants\UserErrorConstantsInterface;
use Mickadoo\Yarnyard\Library\Exception\NonCriticalException;
use Mickadoo\Yarnyard\Library\Validator\AbstractValidator;

class UserValidator extends AbstractValidator implements UserErrorConstantsInterface
{

    /**
     * @var User
     */
    protected $user;

    /**
     * @return bool
     * @throws \Exception
     */
    public function isValid()
    {
        $user = $this->user;

        if (!$user) {
            throw new NonCriticalException(self::ERROR_USER_NOT_SET, 500);
        }

        if (!$this->isUserEmailValid($user->getEmail())) {
            return false;
        }

        if (!$this->isUserUsernameValid($user->getUsername())) {
            return false;
        }

        if (!$this->isUserPasswordValid($user->getPassword())) {
            return false;
        }

        return true;
    }

    /**
     * @param string $email
     * @return bool
     */
    private function isUserEmailValid($email)
    {
        if ($email == '') {
            $this->setErrorResponse(self::ERROR_USER_EMAIL_NOT_SET);

            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setErrorResponse(self::ERROR_USER_EMAIL_INVALID);

            return false;
        }

        if (!$this->isStringAsciiOnly($email)) {
            $this->setErrorResponse(self::ERROR_USER_EMAIL_CONTAINS_NON_ASCII);

            return false;
        }

        if (count($this->getUserRepository()->findBy([User::USER_FIELD_EMAIL => $email])) > 0) {
            $this->setErrorResponse(self::ERROR_USER_EMAIL_ALREADY_EXISTS);

            return false;
        }


        return true;
    }

    /**
     * @param string $username
     * @return bool
     */
    private function isUserUsernameValid($username)
    {
        if ($username == '') {
            $this->setErrorResponse(self::ERROR_USER_USERNAME_NOT_SET);

            return false;
        }

        if (strlen($username) < 6) {
            $this->setErrorResponse(self::ERROR_USER_USERNAME_TOO_SHORT);

            return false;
        }

        if (strlen($username) > 16) {
            $this->setErrorResponse(self::ERROR_USER_USERNAME_TOO_LONG);

            return false;
        }

        if (!$this->isStringAsciiOnly($username)) {
            $this->setErrorResponse(self::ERROR_USER_USERNAME_CONTAINS_NON_ASCII);

            return false;
        }

        if (count($this->getUserRepository()->findBy([User::USER_FIELD_USERNAME => $username])) > 0) {
            $this->setErrorResponse(self::ERROR_USER_USERNAME_ALREADY_EXISTS);

            return false;
        }

        return true;
    }

    private function isUserPasswordValid($password)
    {
        if ($password == '') {
            $this->setErrorResponse(self::ERROR_USER_PASSWORD_NOT_SET);

            return false;
        }

        if (strlen($password) < 5) {
            $this->setErrorResponse(self::ERROR_USER_PASSWORD_TOO_SHORT);

            return false;
        }

        if (strlen($password) > 55) {
            $this->setErrorResponse(self::ERROR_USER_PASSWORD_TOO_LONG);

            return false;
        }

        if (!$this->isStringAsciiOnly($password)) {
            $this->setErrorResponse(self::ERROR_USER_PASSWORD_CONTAINS_NON_ASCII);

            return false;
        }

        return true;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

}