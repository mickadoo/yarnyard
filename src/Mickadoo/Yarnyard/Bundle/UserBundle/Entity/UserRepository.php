<?php

namespace Mickadoo\Yarnyard\Bundle\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

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

}