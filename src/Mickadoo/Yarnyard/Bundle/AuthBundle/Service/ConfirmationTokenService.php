<?php

namespace Mickadoo\Yarnyard\Bundle\AuthBundle\Service;

use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationToken;
use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationTokenRepository;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\Role;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\RoleRepository;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\User;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\UserRepository;
use Mickadoo\Yarnyard\Library\Exception\YarnyardException;

class ConfirmationTokenService
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
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @param UserRepository $userRepository
     * @param ConfirmationTokenRepository $confirmationTokenRepository
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        UserRepository $userRepository,
        ConfirmationTokenRepository $confirmationTokenRepository,
        RoleRepository $roleRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->confirmationTokenRepository = $confirmationTokenRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param int $userId
     * @param string $token
     * @throws YarnyardException
     */
    public function confirmToken($userId, $token)
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new YarnyardException('ERR_USER_NOT_FOUND');
        }

        /** @var ConfirmationToken $token */
        $token = $this->confirmationTokenRepository->findOneBy([
            'token' => $token,
            'user' => $user
        ]);

        if (!$token) {
            throw new YarnyardException('ERR_TOKEN_NOT_FOUND');
        }

        $this->roleRepository->addRoleForUser($user, Role::ACTIVE_USER);
        $this->confirmationTokenRepository->delete($token);
    }
}