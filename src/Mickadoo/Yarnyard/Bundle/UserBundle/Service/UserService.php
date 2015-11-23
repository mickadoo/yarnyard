<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Service;

use Mickadoo\Yarnyard\Bundle\UserBundle\Constants\UserErrors;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\UserRepository;
use Mickadoo\Yarnyard\Library\ErrorConstants\Errors;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
use Mickadoo\Yarnyard\Library\StringHelper\StringHelper;

class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var StringHelper
     */
    protected $stringHelper;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param StringHelper $stringHelper
     */
    public function __construct(UserRepository $userRepository, StringHelper $stringHelper)
    {
        $this->userRepository = $userRepository;
        $this->stringHelper = $stringHelper;
    }

    /**
     * @param $username
     * @param $email
     * @return User
     * @throws YarnyardException
     */
    public function create($username, $email)
    {
        $user = new User();

        $this->setUsername($user, $username);
        $this->setEmail($user, $email);

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @param User $user
     * @param $username
     * @param $email
     * @return User
     * @throws YarnyardException
     */
    public function update(User $user, $username, $email)
    {
        $this->setUsername($user, $username);
        $this->setEmail($user, $email);

        return $user;
    }

    /**
     * @param User $user
     * @param $email
     * @throws YarnyardException
     */
    public function setEmail(User $user, $email)
    {
        if ($email == '') {
            throw new YarnyardException(Errors::ERROR_USER_EMAIL_NOT_SET);
        }

        if ($email === $user->getEmail()) {
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new YarnyardException(Errors::ERROR_USER_EMAIL_INVALID);
        }

        if (!$this->stringHelper->isAsciiOnly($email)) {
            throw new YarnyardException(Errors::ERROR_USER_EMAIL_CONTAINS_NON_ASCII);
        }

        if (count($this->userRepository->findBy([User::USER_FIELD_EMAIL => $email])) > 0) {
            throw new YarnyardException(Errors::ERROR_USER_EMAIL_ALREADY_EXISTS);
        }

        $user->setEmail($email);
    }

    /**
     * @param User $user
     * @param $username
     * @throws YarnyardException
     */
    public function setUsername(User $user, $username)
    {
        if ($username === '') {
            throw new YarnyardException(Errors::ERROR_USER_USERNAME_NOT_SET);
        }

        if ($username === $user->getUsername()) {
            return;
        }

        if (strlen($username) < 5) {
            throw new YarnyardException(Errors::ERROR_USER_USERNAME_TOO_SHORT);
        }

        if (strlen($username) > 16) {
            throw new YarnyardException(Errors::ERROR_USER_USERNAME_TOO_LONG);
        }

        if (!$this->stringHelper->isAsciiOnly($username)) {
            throw new YarnyardException(Errors::ERROR_USER_USERNAME_CONTAINS_NON_ASCII);
        }

        if (count($this->userRepository->findBy([User::USER_FIELD_USERNAME => $username])) > 0) {
            throw new YarnyardException(UserErrors::ERROR_USER_USERNAME_ALREADY_EXISTS);
        }

        $user->setUsername($username);
    }
}