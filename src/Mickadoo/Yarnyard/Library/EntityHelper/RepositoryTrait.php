<?php

namespace Mickadoo\Yarnyard\Library\EntityHelper;

use Mickadoo\Yarnyard\Bundle\UserBundle\Entity\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait RepositoryTrait
{

    /**
     * @var UserRepository
     */
    protected  $userRepository;

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
     * @return ContainerInterface
     */
    private function getContainer()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return $this->container;
    }

}