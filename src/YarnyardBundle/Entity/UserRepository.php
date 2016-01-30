<?php

namespace YarnyardBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return User
     */
    public function save(User $user)
    {
        $this->_em->persist($user);
        $this->_em->flush($user);

        return $user;
    }

    /**
     * @param User $user
     * @return User
     */
    public function update(User $user)
    {
        $this->_em->flush($user);

        return $user;
    }
}
