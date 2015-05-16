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
        if (! $this->user) {
            throw new NonCriticalException(self::ERROR_USER_NOT_SET, 500);
        }

        if ($this->user->getEmail() == '') {
            $this->setErrorResponse(self::ERROR_USER_EMAIL_NOT_SET);
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