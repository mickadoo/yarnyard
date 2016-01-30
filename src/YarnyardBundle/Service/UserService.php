<?php

namespace YarnyardBundle\Service;

use YarnyardBundle\Entity\User;
use YarnyardBundle\Entity\UserRepository;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;

class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param $uuid
     * @return User
     * @throws YarnyardException
     */
    public function create($uuid)
    {
        $user = new User($uuid);
        $this->userRepository->save($user);

        return $user;
    }
}