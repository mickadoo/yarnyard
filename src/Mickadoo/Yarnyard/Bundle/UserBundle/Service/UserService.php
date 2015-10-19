<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Service;

use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationTokenRepository;
use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\UserErrors;
use Mickadoo\Yarnyard\Bundle\UserBundle\ConstantsInterface\UserEvents;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\UserRepository;
use Mickadoo\Yarnyard\Bundle\UserBundle\Event\UserCreatedEvent;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;
use Mickadoo\Yarnyard\Library\StringHelper\StringHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ConfirmationTokenRepository
     */
    protected $confirmationTokenRepository;

    /**
     * @var UserPasswordEncoder
     */
    protected $passwordEncoder;

    /**
     * @var StringHelper
     */
    protected $stringHelper;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param UserRepository $userRepository
     * @param ConfirmationTokenRepository $confirmationTokenRepository
     * @param UserPasswordEncoder $passwordEncoder
     * @param StringHelper $stringHelper
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        UserRepository $userRepository,
        ConfirmationTokenRepository $confirmationTokenRepository,
        UserPasswordEncoder $passwordEncoder,
        StringHelper $stringHelper,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->userRepository = $userRepository;
        $this->confirmationTokenRepository = $confirmationTokenRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->stringHelper = $stringHelper;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $username
     * @param $email
     * @param $password
     * @return User
     * @throws YarnyardException
     */
    public function create($username, $email, $password)
    {
        $user = new User();

        $this->setUsername($user, $username);
        $this->setEmail($user, $email);
        $this->setPassword($user, $password);
        $user->setSalt(uniqid(mt_rand(), true));

        $this->userRepository->save($user);

        $confirmationToken = $this->confirmationTokenRepository->createTokenForUser($user);

        $newUserEvent = new UserCreatedEvent($confirmationToken);
        $this->eventDispatcher->dispatch(UserEvents::USER_CREATED, $newUserEvent);

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
            throw new YarnyardException(UserErrors::ERROR_USER_EMAIL_NOT_SET);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new YarnyardException(UserErrors::ERROR_USER_EMAIL_INVALID);
        }

        if (!$this->stringHelper->isAsciiOnly($email)) {
            throw new YarnyardException(UserErrors::ERROR_USER_EMAIL_CONTAINS_NON_ASCII);
        }

        if (count($this->userRepository->findBy([User::USER_FIELD_EMAIL => $email])) > 0) {
            throw new YarnyardException(UserErrors::ERROR_USER_EMAIL_ALREADY_EXISTS);
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
        if ($username == '') {
            throw new YarnyardException(UserErrors::ERROR_USER_USERNAME_NOT_SET);
        }

        if (strlen($username) < 5) {
            throw new YarnyardException(UserErrors::ERROR_USER_USERNAME_TOO_SHORT);
        }

        if (strlen($username) > 16) {
            throw new YarnyardException(UserErrors::ERROR_USER_USERNAME_TOO_LONG);
        }

        if (!$this->stringHelper->isAsciiOnly($username)) {
            throw new YarnyardException(UserErrors::ERROR_USER_USERNAME_CONTAINS_NON_ASCII);
        }

        if (count($this->userRepository->findBy([User::USER_FIELD_USERNAME => $username])) > 0) {
            throw new YarnyardException(UserErrors::ERROR_USER_USERNAME_ALREADY_EXISTS);
        }

        $user->setUsername($username);
    }

    /**
     * @param User $user
     * @param $password
     * @throws YarnyardException
     */
    public function setPassword(User $user, $password)
    {
        if ($password == '') {
            throw new YarnyardException(UserErrors::ERROR_USER_PASSWORD_NOT_SET);
        }

        if (strlen($password) < 5) {
            throw new YarnyardException(UserErrors::ERROR_USER_PASSWORD_TOO_SHORT);
        }

        if (!$this->stringHelper->isAsciiOnly($password)) {
            throw new YarnyardException(UserErrors::ERROR_USER_PASSWORD_CONTAINS_NON_ASCII);
        }

        if (strlen($password) > 55) {
            throw new YarnyardException(UserErrors::ERROR_USER_PASSWORD_TOO_LONG);
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
    }
}