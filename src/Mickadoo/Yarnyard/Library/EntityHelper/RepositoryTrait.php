<?php

namespace Mickadoo\Yarnyard\Library\EntityHelper;

use YarnyardBundle\Entity\RoleRepository;
use YarnyardBundle\Entity\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait RepositoryTrait
{
    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->getContainer()->get('user.repository');
    }

    /**
     * @return RoleRepository
     */
    public function getRoleRepository()
    {
        return $this->getContainer()->get('yarnyard.user.role.repository');
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
