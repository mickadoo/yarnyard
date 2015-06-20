<?php

namespace Mickadoo\Yarnyard\Library\EntityHelper;

use Mickadoo\Yarnyard\Bundle\AuthBundle\Entity\ConfirmationTokenRepository;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\RoleRepository;
use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait RepositoryTrait
{

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var ConfirmationTokenRepository
     */
    protected $confirmationTokenRepository;

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        if (! isset($this->userRepository)) {
            $this->userRepository = $this->getContainer()->get('yarnyard.user.user.repository');
        }

        return $this->userRepository;
    }


    /**
     * @return RoleRepository
     */
    public function getRoleRepository()
    {
        if (! isset($this->roleRepository)) {
            $this->roleRepository = $this->getContainer()->get('yarnyard.user.role.repository');
        }

        return $this->roleRepository;
    }

    /**
     * @return ConfirmationTokenRepository
     */
    public function getConfirmationTokenRepository()
    {
        if (! isset($this->confirmationTokenRepository)) {
            $this->confirmationTokenRepository = $this
                ->getContainer()
                ->get('yarnyard.auth.confirmation_token.repository');
        }

        return $this->confirmationTokenRepository;
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->container;
    }

}
