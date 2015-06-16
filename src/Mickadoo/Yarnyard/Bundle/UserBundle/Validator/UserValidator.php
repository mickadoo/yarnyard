<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Validator;

use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\UserErrorConstantsInterface;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
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
            throw new YarnyardException(self::ERROR_USER_NOT_SET, 500);
        }

        if (!$this->isUserEmailValid()) {
            return false;
        }

        if (!$this->isUserUsernameValid()) {
            return false;
        }

        if (!$this->isUserPasswordValid()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isUserEmailValid()
    {
        $email = $this->user->getEmail();

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

        return true;
    }

    /**
     * @return bool
     */
    protected function isUserUsernameValid()
    {
        $username = $this->user->getUsername();

        if ($username == '') {
            $this->setErrorResponse(self::ERROR_USER_USERNAME_NOT_SET);

            return false;
        }

        if (strlen($username) < 5) {
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

        return true;
    }

    protected function isUserPasswordValid()
    {
        $password = $this->user->getPassword();

        if ($password == '') {
            $this->setErrorResponse(self::ERROR_USER_PASSWORD_NOT_SET);

            return false;
        }

        if (strlen($password) < 5) {
            $this->setErrorResponse(self::ERROR_USER_PASSWORD_TOO_SHORT);

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