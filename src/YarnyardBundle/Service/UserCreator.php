<?php

namespace YarnyardBundle\Service;

use Doctrine\ORM\EntityManager;
use YarnyardBundle\Entity\User;
use YarnyardBundle\Exception\YarnyardException;

class UserCreator
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $uuid
     *
     * @return User
     *
     * @throws YarnyardException
     */
    public function create($uuid)
    {
        $user = new User($uuid);
        $this->manager->persist($user);
        $this->manager->flush($user);

        return $user;
    }
}
